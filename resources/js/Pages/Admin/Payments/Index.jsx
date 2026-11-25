import AppLayout from "@/components/layout/AppLayout";
import { Card, CardContent } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";

const statusVariant = { pending: 'warning', paid: 'success', failed: 'destructive' };

export default function PaymentsIndex({ payments }) {
    return (
        <AppLayout title="Payments" breadcrumbs={[{ label: "Dashboard", url: "/dashboard" }, { label: "Payments" }]}>
            <h1 className="text-xl font-semibold mb-6">Payments</h1>

            <Card>
                <CardContent className="p-0">
                    {payments.length === 0 ? (
                        <p className="text-sm text-muted-foreground p-6">No payments yet.</p>
                    ) : (
                        <table className="w-full text-sm">
                            <thead className="border-b">
                                <tr className="text-muted-foreground text-xs uppercase tracking-wider">
                                    <th className="text-left p-4">Bill ID</th>
                                    <th className="text-left p-4">Order</th>
                                    <th className="text-left p-4">Customer</th>
                                    <th className="text-left p-4">Amount</th>
                                    <th className="text-left p-4">Status</th>
                                    <th className="text-left p-4">Paid At</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-border">
                                {payments.map(payment => (
                                    <tr key={payment.id} className="hover:bg-muted/30 transition">
                                        <td className="p-4 font-mono text-xs">{payment.billplz_bill_id}</td>
                                        <td className="p-4 font-mono">{payment.order_number}</td>
                                        <td className="p-4">{payment.customer_name}</td>
                                        <td className="p-4 font-semibold">RM {parseFloat(payment.amount).toFixed(2)}</td>
                                        <td className="p-4">
                                            <Badge variant={statusVariant[payment.status] ?? 'secondary'} className="capitalize">
                                                {payment.status}
                                            </Badge>
                                        </td>
                                        <td className="p-4 text-muted-foreground">{payment.paid_at ?? '—'}</td>
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
