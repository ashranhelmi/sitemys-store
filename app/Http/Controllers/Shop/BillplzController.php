<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Mail\LowStockAlert;
use App\Mail\NewOrderAlert;
use App\Mail\OrderConfirmation;
use App\Mail\PaymentFailed;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BillplzController extends Controller
{
    private string $apiKey;
    private string $collectionId;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey       = (string) config('services.billplz.api_key');
        $this->collectionId = (string) config('services.billplz.collection_id');
        $this->baseUrl      = config('services.billplz.sandbox', true)
            ? 'https://www.billplz-sandbox.com/api/v3'
            : 'https://www.billplz.com/api/v3';
    }

    public function createBill(Order $order)
    {
        // Public demo: no gateway. A demo bill points at our own "choose the outcome" page instead of Billplz.
        if (config('demo.enabled')) {
            $billId = 'demo-' . bin2hex(random_bytes(6));
            $order->update(['billplz_bill_id' => $billId]);
            Payment::updateOrCreate(
                ['order_id' => $order->id],
                ['billplz_bill_id' => $billId, 'amount' => $order->total, 'status' => 'pending', 'billplz_data' => ['id' => $billId, 'demo' => true]]
            );
            session()->forget('cart');

            return redirect(\Illuminate\Support\Facades\URL::signedRoute('demo.pay', $order));
        }

        $amountCents = (int) ($order->total * 100);

        $response = Http::withBasicAuth($this->apiKey, '')
            ->post("{$this->baseUrl}/bills", [
                'collection_id'     => $this->collectionId,
                'email'             => $order->customer_email,
                'mobile'            => $order->customer_phone,
                'name'              => $order->customer_name,
                'amount'            => $amountCents,
                'callback_url'      => route('billplz.webhook'),
                'redirect_url'      => route('billplz.callback'),
                'description'       => 'Order ' . $order->order_number,
                'reference_1_label' => 'Order',
                'reference_1'       => $order->order_number,
            ]);

        if ($response->failed()) {
            Log::error('Billplz bill creation failed', [
                'order'    => $order->order_number,
                'response' => $response->body(),
            ]);
            return redirect()->route('checkout.index')->with('error', 'Payment gateway error. Please try again.');
        }

        $bill = $response->json();

        $order->update(['billplz_bill_id' => $bill['id']]);

        // Update existing payment record or create new one
        Payment::updateOrCreate(
            ['order_id' => $order->id],
            [
                'billplz_bill_id' => $bill['id'],
                'amount'          => $order->total,
                'status'          => 'pending',
                'billplz_data'    => $bill,
            ]
        );

        session()->forget('cart');

        return redirect($bill['url']);
    }

    /**
     * Browser redirect back from Billplz. The query string can be edited by anyone,
     * so the payment state comes from Billplz's API, never from the URL.
     */
    public function callback(Request $request)
    {
        $billId  = (string) $request->input('billplz.id');
        $payment = $billId ? Payment::where('billplz_bill_id', $billId)->first() : null;

        if (!$payment) {
            return redirect()->route('shop.index');
        }

        $order = $payment->order;
        $bill  = $this->fetchBill($billId);
        $paid  = $bill && $this->markPaidIfVerified($payment, $bill);

        if ($paid || $payment->fresh()->status === 'paid') {
            return redirect($order->thankYouUrl());
        }

        try {
            Mail::to($order->customer_email)->send(new PaymentFailed($order));
        } catch (\Exception $e) {
            Log::error('Failed to send payment failed email', ['error' => $e->getMessage()]);
        }

        return redirect($order->retryUrl());
    }

    public function retry(Order $order)
    {
        return view('shop.payment-retry', compact('order'));
    }

    public function renew(Order $order)
    {
        // Create a fresh Billplz bill (old one may have expired)
        return $this->createBill($order);
    }

    /**
     * Server-to-server callback from Billplz. Like the redirect, its body is only a hint:
     * the bill is re-read from Billplz's API before an order is marked paid.
     */
    public function webhook(Request $request)
    {
        $billId  = (string) $request->input('id');
        $payment = $billId ? Payment::where('billplz_bill_id', $billId)->first() : null;

        if (!$payment) {
            return response('OK', 200);
        }

        $bill = $this->fetchBill($billId);
        if (!$bill) {
            return response('Could not verify bill', 502); // Billplz retries failed callbacks
        }

        $this->markPaidIfVerified($payment, $bill);

        return response('OK', 200);
    }

    /** Demo only: the visitor chose "Pay" on the demo payment page. Same paid path as a verified Billplz bill. */
    public function settleDemoPayment(Payment $payment): bool
    {
        abort_unless(config('demo.enabled') && str_starts_with((string) $payment->billplz_bill_id, 'demo-'), 404);

        return $this->markPaidIfVerified($payment, [
            'id'          => $payment->billplz_bill_id,
            'paid'        => true,
            'paid_amount' => (int) round($payment->amount * 100),
            'paid_at'     => now()->toIso8601String(),
            'demo'        => true,
        ]);
    }

    public function sendPaymentFailed(Order $order): void
    {
        try {
            Mail::to($order->customer_email)->send(new PaymentFailed($order));
        } catch (\Exception $e) {
            Log::error('Failed to send payment failed email', ['error' => $e->getMessage()]);
        }
    }

    /** GET /bills/{id} from Billplz with our API key. Null when it can't be read. */
    private function fetchBill(string $billId): ?array
    {
        if (!$this->apiKey || !preg_match('/^[A-Za-z0-9_-]{4,64}$/', $billId)) {
            return null;
        }

        try {
            $response = Http::withBasicAuth($this->apiKey, '')->timeout(15)->get("{$this->baseUrl}/bills/{$billId}");
        } catch (\Exception $e) {
            Log::error('Billplz bill lookup failed', ['bill_id' => $billId, 'error' => $e->getMessage()]);
            return null;
        }

        return $response->successful() ? $response->json() : null;
    }

    /** Marks the payment paid only if Billplz says paid, for this bill, for the full amount. Returns true when paid. */
    private function markPaidIfVerified(Payment $payment, array $bill): bool
    {
        $expectedCents = (int) round($payment->amount * 100);
        $isPaid = ($bill['paid'] ?? false) === true
            && ($bill['id'] ?? null) === $payment->billplz_bill_id
            && (int) ($bill['paid_amount'] ?? $bill['amount'] ?? 0) === $expectedCents;

        if (!$isPaid) {
            if (($bill['paid'] ?? false) === true) {
                Log::warning('Billplz bill paid but did not match the order', ['bill_id' => $payment->billplz_bill_id, 'expected' => $expectedCents]);
            }
            return false;
        }

        // Atomic claim so a redirect and a webhook arriving together can't both deduct stock.
        $claimed = Payment::where('id', $payment->id)->where('status', '!=', 'paid')->update([
            'status'       => 'paid',
            'paid_at'      => !empty($bill['paid_at']) ? \Carbon\Carbon::parse($bill['paid_at']) : now(),
            'billplz_data' => json_encode(array_merge($payment->billplz_data ?? [], ['verified' => $bill])),
        ]);

        if ($claimed) {
            $order = $payment->order;
            $order->update(['status' => 'paid']);
            $this->deductStock($order);

            try {
                Mail::to($order->customer_email)->send(new OrderConfirmation($order));
                Mail::to(config('mail.from.address'))->send(new NewOrderAlert($order));
            } catch (\Exception $e) {
                Log::error('Failed to send order emails', ['error' => $e->getMessage()]);
            }
        }

        return true;
    }

    private function deductStock(Order $order): void
    {
        foreach ($order->items as $item) {
            if ($item->product_id) {
                Product::where('id', $item->product_id)
                    ->where('stock', '>', 0)
                    ->decrement('stock', $item->quantity);

                $product = Product::find($item->product_id);
                if ($product && $product->stock <= 0) {
                    try {
                        Mail::to(config('mail.from.address'))->send(new LowStockAlert($product));
                    } catch (\Exception $e) {
                        Log::error('Failed to send low stock alert', ['error' => $e->getMessage()]);
                    }
                }
            }
        }
    }
}
