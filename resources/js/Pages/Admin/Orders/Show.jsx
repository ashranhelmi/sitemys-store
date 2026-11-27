import { useForm, Link, router } from "@inertiajs/react";
import AppLayout from "@/components/layout/AppLayout";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";

const STATUSES = ['pending', 'paid', 'processing', 'packed', 'failed', 'shipped', 'delivered', 'completed', 'cancelled', 'refunded', 'expired'];

const statusVariant = {
    pending:    'warning',
    paid:       'default',
    processing: 'default',
    packed:     'default',
    failed:     'destructive',
    shipped:    'secondary',
    delivered:  'success',
    completed:  'success',
    cancelled:  'destructive',
    refunded:   'destructive',
    expired:    'secondary',
};

export default function OrderShow({ order }) {
    const { data, setData, put, processing } = useForm({
        status:          order.status,
        courier:         order.courier ?? '',
        tracking_number: order.tracking_number ?? '',
        notes:           order.notes ?? '',
    });

    const updateOrder = () => put(route('admin.orders.update', order.id));

    const resendPaymentLink = () => {
        router.post(route('admin.orders.renew', order.id));
    };

    return (
        <AppLayout title={order.order_number} breadcrumbs={[
            { label: "Dashboard", url: "/dashboard" },
            { label: "Orders", url: route('admin.orders.index') },
            { label: order.order_number },
        ]}>
            <div className="flex items-center justify-between mb-6">
                <div>
                    <h1 className="text-xl font-semibold font-mono">{order.order_number}</h1>
                    <p className="text-sm text-muted-foreground mt-1">{order.created_at}</p>
                </div>
                <Badge variant={statusVariant[order.status] ?? 'secondary'} className="capitalize text-sm px-3 py-1">
                    {order.status}
                </Badge>
            </div>

            <div className="grid md:grid-cols-3 gap-6">
                <div className="md:col-span-2 space-y-6">
                    {/* Items */}
                    <Card>
                        <CardHeader><CardTitle>Items</CardTitle></CardHeader>
                        <CardContent className="p-0">
                            {/* Phones: one line per item */}
                            <ul className="sm:hidden divide-y divide-border border-b">
                                {order.items.map(item => (
                                    <li key={item.id} className="p-4 text-sm">
                                        <p className="font-medium">{item.product_name}</p>
                                        <div className="mt-1 flex justify-between gap-3">
                                            <span className="text-muted-foreground">Size {item.size ?? '—'} · Qty {item.quantity}</span>
                                            <span className="shrink-0">RM {parseFloat(item.subtotal).toFixed(2)}</span>
                                        </div>
                                    </li>
                                ))}
                            </ul>
                            <table className="hidden sm:table w-full text-sm">
                                <thead className="border-b">
                                    <tr className="text-muted-foreground text-xs uppercase tracking-wider">
                                        <th className="text-left p-4">Product</th>
                                        <th className="text-left p-4">Size</th>
                                        <th className="text-left p-4">Qty</th>
                                        <th className="text-right p-4">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-border">
                                    {order.items.map(item => (
                                        <tr key={item.id}>
                                            <td className="p-4 font-medium">{item.product_name}</td>
                                            <td className="p-4 text-muted-foreground">{item.size ?? '—'}</td>
                                            <td className="p-4">{item.quantity}</td>
                                            <td className="p-4 text-right">RM {parseFloat(item.subtotal).toFixed(2)}</td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                            <div className="p-4 border-t space-y-1 text-sm">
                                <div className="flex justify-between text-muted-foreground">
                                    <span>Subtotal</span><span>RM {parseFloat(order.subtotal).toFixed(2)}</span>
                                </div>
                                <div className="flex justify-between text-muted-foreground">
                                    <span>Shipping</span><span>RM {parseFloat(order.shipping).toFixed(2)}</span>
                                </div>
                                <div className="flex justify-between font-semibold text-base pt-1">
                                    <span>Total</span><span>RM {parseFloat(order.total).toFixed(2)}</span>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Payment */}
                    {order.payment && (
                        <Card>
                            <CardHeader><CardTitle>Payment</CardTitle></CardHeader>
                            <CardContent className="text-sm space-y-2">
                                <div className="flex justify-between"><span className="text-muted-foreground">Bill ID</span><span className="font-mono">{order.payment.billplz_bill_id}</span></div>
                                <div className="flex justify-between"><span className="text-muted-foreground">Amount</span><span>RM {parseFloat(order.payment.amount).toFixed(2)}</span></div>
                                <div className="flex justify-between items-center">
                                    <span className="text-muted-foreground">Status</span>
                                    <Badge variant={order.payment.status === 'paid' ? 'success' : 'warning'} className="capitalize">{order.payment.status}</Badge>
                                </div>
                                {order.payment.paid_at && (
                                    <div className="flex justify-between"><span className="text-muted-foreground">Paid At</span><span>{order.payment.paid_at}</span></div>
                                )}
                            </CardContent>
                        </Card>
                    )}

                    {/* Shipment tracking */}
                    {(order.courier || order.tracking_number) && (
                        <Card>
                            <CardHeader><CardTitle>Shipment</CardTitle></CardHeader>
                            <CardContent className="text-sm space-y-2">
                                {order.courier && <div className="flex justify-between"><span className="text-muted-foreground">Courier</span><span>{order.courier}</span></div>}
                                {order.tracking_number && <div className="flex justify-between"><span className="text-muted-foreground">Tracking No.</span><span className="font-mono">{order.tracking_number}</span></div>}
                            </CardContent>
                        </Card>
                    )}

                    {/* Notes */}
                    {order.notes && (
                        <Card>
                            <CardHeader><CardTitle>Notes</CardTitle></CardHeader>
                            <CardContent>
                                <p className="text-sm text-muted-foreground whitespace-pre-wrap">{order.notes}</p>
                            </CardContent>
                        </Card>
                    )}
                </div>

                <div className="space-y-6">
                    {/* Customer */}
                    <Card>
                        <CardHeader><CardTitle>Customer</CardTitle></CardHeader>
                        <CardContent className="text-sm space-y-2">
                            <p className="font-medium">{order.customer_name}</p>
                            <p className="text-muted-foreground">{order.customer_email}</p>
                            <p className="text-muted-foreground">{order.customer_phone}</p>
                        </CardContent>
                    </Card>

                    {/* Shipping address */}
                    <Card>
                        <CardHeader><CardTitle>Shipping Address</CardTitle></CardHeader>
                        <CardContent className="text-sm text-muted-foreground space-y-1">
                            <p>{order.address}</p>
                            <p>{order.city}, {order.state} {order.postcode}</p>
                        </CardContent>
                    </Card>

                    {/* Update order */}
                    <Card>
                        <CardHeader><CardTitle>Update Order</CardTitle></CardHeader>
                        <CardContent className="space-y-3">
                            <div>
                                <label className="text-xs text-muted-foreground mb-1 block">Status</label>
                                <Select value={data.status} onValueChange={v => setData('status', v)}>
                                    <SelectTrigger><SelectValue /></SelectTrigger>
                                    <SelectContent>
                                        {STATUSES.map(s => (
                                            <SelectItem key={s} value={s} className="capitalize">{s}</SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>

                            <div>
                                <label className="text-xs text-muted-foreground mb-1 block">Courier</label>
                                <Input
                                    placeholder="e.g. J&T, Poslaju, DHL"
                                    value={data.courier}
                                    onChange={e => setData('courier', e.target.value)}
                                />
                            </div>

                            <div>
                                <label className="text-xs text-muted-foreground mb-1 block">Tracking Number</label>
                                <Input
                                    placeholder="e.g. JT1234567890"
                                    value={data.tracking_number}
                                    onChange={e => setData('tracking_number', e.target.value)}
                                />
                            </div>

                            <div>
                                <label className="text-xs text-muted-foreground mb-1 block">Admin Notes</label>
                                <textarea
                                    rows={3}
                                    placeholder="Internal notes, fulfillment remarks..."
                                    value={data.notes}
                                    onChange={e => setData('notes', e.target.value)}
                                    className="w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring resize-none"
                                />
                            </div>

                            <Button className="w-full" onClick={updateOrder} disabled={processing}>
                                Save Changes
                            </Button>

                            {/* Resend payment link — only for pending orders */}
                            {['pending', 'failed'].includes(order.status) && (
                                <Button variant="outline" className="w-full" onClick={resendPaymentLink}>
                                    Resend Payment Link
                                </Button>
                            )}
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AppLayout>
    );
}
