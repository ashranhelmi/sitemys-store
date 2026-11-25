<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    private array $customers = [
        ['name' => 'Ahmad Faiz',        'email' => 'ahmad.faiz@example.com',     'phone' => '0100000001', 'address' => '12, Jalan Damai 1',           'city' => 'Kuala Lumpur',  'state' => 'Kuala Lumpur', 'postcode' => '55100'],
        ['name' => 'Nurul Ain',         'email' => 'nurul.ain@example.com',      'phone' => '0100000002', 'address' => '45, Taman Melawati',           'city' => 'Ampang',        'state' => 'Selangor',     'postcode' => '68000'],
        ['name' => 'Haziq Rahman',      'email' => 'haziq.r@example.com',      'phone' => '0100000003', 'address' => '7, Lorong Utama 3',            'city' => 'Petaling Jaya', 'state' => 'Selangor',     'postcode' => '47810'],
        ['name' => 'Syafiqah Zulkifli', 'email' => 'syafiqah.z@example.com',    'phone' => '0100000004', 'address' => '22, Jalan Bunga Raya',         'city' => 'Subang Jaya',   'state' => 'Selangor',     'postcode' => '47500'],
        ['name' => 'Afiq Danial',       'email' => 'afiq.d@example.com',        'phone' => '0100000005', 'address' => '88, Residensi Damansara',      'city' => 'Damansara',     'state' => 'Selangor',     'postcode' => '47410'],
        ['name' => 'Izzaty Hassan',     'email' => 'izzaty.h@example.com',      'phone' => '0100000006', 'address' => '3, Jalan Tuanku Abdul Halim',  'city' => 'Johor Bahru',   'state' => 'Johor',        'postcode' => '80300'],
        ['name' => 'Ridhwan Aziz',      'email' => 'ridhwan.a@example.com',     'phone' => '0100000007', 'address' => '15, Taman Desa Jaya',          'city' => 'Kepong',        'state' => 'Kuala Lumpur', 'postcode' => '52100'],
        ['name' => 'Farhana Idris',     'email' => 'farhana.i@example.com',     'phone' => '0100000008', 'address' => '101, Jalan Ipoh',              'city' => 'Kuala Lumpur',  'state' => 'Kuala Lumpur', 'postcode' => '51200'],
        ['name' => 'Zulaikha Mohamad',  'email' => 'zulaikha.m@example.com',    'phone' => '0100000009', 'address' => '34, Bandar Sunway',            'city' => 'Subang Jaya',   'state' => 'Selangor',     'postcode' => '47500'],
        ['name' => 'Hafeez Malik',      'email' => 'hafeez.m@example.com',      'phone' => '0100000010', 'address' => '67, Taman OUG',                'city' => 'Kuala Lumpur',  'state' => 'Kuala Lumpur', 'postcode' => '58200'],
        ['name' => 'Dina Sulaiman',     'email' => 'dina.s@example.com',        'phone' => '0100000011', 'address' => '9, Jalan Putra',               'city' => 'Kuala Lumpur',  'state' => 'Kuala Lumpur', 'postcode' => '50350'],
        ['name' => 'Nabil Ariffin',     'email' => 'nabil.a@example.com',       'phone' => '0100000012', 'address' => '77, Taman Bukit Indah',        'city' => 'Johor Bahru',   'state' => 'Johor',        'postcode' => '81300'],
        ['name' => 'Liyana Yusoff',     'email' => 'liyana.y@example.com',      'phone' => '0100000013', 'address' => '23, Jalan Wangsa Maju',        'city' => 'Kuala Lumpur',  'state' => 'Kuala Lumpur', 'postcode' => '53300'],
        ['name' => 'Irfan Shafiq',      'email' => 'irfan.s@example.com',       'phone' => '0100000014', 'address' => '55, Taman Putra Perdana',      'city' => 'Puchong',       'state' => 'Selangor',     'postcode' => '47100'],
        ['name' => 'Amirah Norzaini',   'email' => 'amirah.n@example.com',    'phone' => '0100000015', 'address' => '8, Jalan Semantan',            'city' => 'Kuala Lumpur',  'state' => 'Kuala Lumpur', 'postcode' => '50490'],
    ];

    // Orders per month, oldest first, ending in the current month (demo growth curve)
    private array $curve = [3, 4, 3, 5, 8, 11, 6, 5, 8, 10, 12, 9];

    public function run(): void
    {
        $products = Product::all();
        $months   = count($this->curve);

        foreach ($this->curve as $i => $count) {
            $start = now()->startOfMonth()->subMonths($months - 1 - $i);
            $end   = $i === $months - 1 ? now()->subHour() : $start->copy()->endOfMonth();

            for ($n = 0; $n < $count; $n++) {
                $this->seedOrder($products, $start, $end);
            }
        }
    }

    private function seedOrder($products, Carbon $start, Carbon $end): void
    {
        $customer  = $this->customers[array_rand($this->customers)];
        $items     = $this->pickItems($products);
        $subtotal  = collect($items)->sum(fn ($i) => $i['unit_price'] * $i['qty']);
        $shipping  = 10.00;
        $total     = $subtotal + $shipping;
        $orderedAt = Carbon::createFromTimestamp(rand($start->timestamp, $end->timestamp));
        $paidAt    = $orderedAt->copy()->addMinutes(rand(5, 25));

        // Checkout links each order to a customer account; the demo data does the same.
        $user = \App\Models\User::firstOrCreate(
            ['email' => $customer['email']],
            ['name' => $customer['name'], 'password' => Str::random(32), 'created_at' => $orderedAt]
        );

        $order = Order::create([
            'order_number'    => config('store.order_prefix') . '-' . strtoupper(Str::random(8)),
            'user_id'         => $user->id,
            'customer_name'   => $customer['name'],
            'customer_email'  => $customer['email'],
            'customer_phone'  => $customer['phone'],
            'address'         => $customer['address'],
            'city'            => $customer['city'],
            'state'           => $customer['state'],
            'postcode'        => $customer['postcode'],
            'subtotal'        => $subtotal,
            'shipping'        => $shipping,
            'total'           => $total,
            'status'          => $this->statusFor($orderedAt),
            'billplz_bill_id' => 'SEED-' . strtoupper(Str::random(10)),
        ]);

        DB::table('orders')->where('id', $order->id)->update([
            'created_at' => $orderedAt->format('Y-m-d H:i:s'),
            'updated_at' => $orderedAt->format('Y-m-d H:i:s'),
        ]);

        foreach ($items as $item) {
            OrderItem::create([
                'order_id'     => $order->id,
                'product_id'   => $item['product_id'],
                'product_name' => $item['product_name'],
                'size'         => $item['size'],
                'unit_price'   => $item['unit_price'],
                'quantity'     => $item['qty'],
                'subtotal'     => $item['unit_price'] * $item['qty'],
            ]);
        }

        $payment = Payment::create([
            'order_id'        => $order->id,
            'billplz_bill_id' => $order->billplz_bill_id,
            'amount'          => $total,
            'status'          => 'paid',
            'paid_at'         => $paidAt,
        ]);

        DB::table('payments')->where('id', $payment->id)->update([
            'created_at' => $paidAt->format('Y-m-d H:i:s'),
            'updated_at' => $paidAt->format('Y-m-d H:i:s'),
        ]);
    }

    private function statusFor(Carbon $orderedAt): string
    {
        $days = $orderedAt->diffInDays(now());
        if ($days < 2)  return ['paid', 'processing'][rand(0, 1)];
        if ($days < 6)  return ['processing', 'packed', 'shipped'][rand(0, 2)];
        if ($days < 14) return ['shipped', 'delivered'][rand(0, 1)];
        return 'completed';
    }

    private function pickItems($products): array
    {
        $usedIds = [];
        $results = [];
        $count   = rand(1, 2);

        for ($i = 0; $i < $count; $i++) {
            $available = $products->whereNotIn('id', $usedIds);
            if ($available->isEmpty()) break;

            $product   = $available->random();
            $usedIds[] = $product->id;

            $results[] = [
                'product_id'   => $product->id,
                'product_name' => $product->name,
                'size'         => $product->sizes[array_rand($product->sizes)],
                'unit_price'   => (float) $product->price,
                'qty'          => rand(1, 2),
            ];
        }

        return $results ?: [[
            'product_id'   => $products->first()->id,
            'product_name' => $products->first()->name,
            'size'         => $products->first()->sizes[0],
            'unit_price'   => (float) $products->first()->price,
            'qty'          => 1,
        ]];
    }
}
