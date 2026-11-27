import { Link } from "@inertiajs/react";
import { useState } from "react";
import AppLayout from "@/components/layout/AppLayout";
import { Card, CardContent } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Eye } from "lucide-react";

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

const FILTERS = ['all', 'pending', 'paid', 'processing', 'packed', 'shipped', 'delivered', 'completed', 'cancelled', 'failed', 'expired', 'refunded'];

export default function OrdersIndex({ orders }) {
    const [activeFilter, setActiveFilter] = useState('all');

    const filtered = activeFilter === 'all'
        ? orders
        : orders.filter(o => o.status === activeFilter);

    return (
        <AppLayout title="Orders" breadcrumbs={[{ label: "Dashboard", url: route('dashboard') }, { label: "Orders" }]}>
            <div className="flex items-center justify-between mb-6">
                <h1 className="text-xl font-semibold">Orders</h1>
                <p className="text-sm text-muted-foreground">{filtered.length} orders</p>
            </div>

            {/* Status filter tabs */}
            <div className="flex gap-1 flex-wrap mb-4">
                {FILTERS.map(f => (
                    <button
                        key={f}
                        onClick={() => setActiveFilter(f)}
                        className={`px-3 py-1 rounded-md text-xs capitalize transition ${
                            activeFilter === f
                                ? 'bg-foreground text-background font-semibold'
                                : 'text-muted-foreground hover:text-foreground hover:bg-muted'
                        }`}
                    >
                        {f === 'all' ? `All (${orders.length})` : `${f} (${orders.filter(o => o.status === f).length})`}
                    </button>
                ))}
            </div>

            <Card>
                <CardContent className="p-0">
                    {filtered.length === 0 ? (
                        <p className="text-sm text-muted-foreground p-6">No orders in this category.</p>
                    ) : (
                        <table className="w-full text-sm">
                            <thead className="border-b">
                                <tr className="text-muted-foreground text-xs uppercase tracking-wider">
                                    <th className="text-left p-4">Order</th>
                                    <th className="text-left p-4">Customer</th>
                                    <th className="text-left p-4">Items</th>
                                    <th className="text-left p-4">Total</th>
                                    <th className="text-left p-4">Status</th>
                                    <th className="text-left p-4">Date</th>
                                    <th className="p-4"></th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-border">
                                {filtered.map(order => (
                                    <tr key={order.id} className="hover:bg-muted/30 transition">
                                        <td className="p-4 font-mono font-medium">{order.order_number}</td>
                                        <td className="p-4">
                                            <p>{order.customer_name}</p>
                                            <p className="text-muted-foreground text-xs">{order.customer_email}</p>
                                        </td>
                                        <td className="p-4">{order.items_count}</td>
                                        <td className="p-4 font-semibold">RM {parseFloat(order.total).toFixed(2)}</td>
                                        <td className="p-4">
                                            <Badge variant={statusVariant[order.status] ?? 'secondary'} className="capitalize">
                                                {order.status}
                                            </Badge>
                                        </td>
                                        <td className="p-4 text-muted-foreground">{order.created_at}</td>
                                        <td className="p-4">
                                            <Link href={route('admin.orders.show', order.id)}>
                                                <Button size="icon" variant="ghost"><Eye className="size-4" /></Button>
                                            </Link>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    )}
                </CardContent>
            </Card>
        </AppLayout>
    );
}
