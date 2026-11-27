import AppLayout from "@/components/layout/AppLayout";
import { Card, CardContent } from "@/components/ui/card";

export default function CustomersIndex({ customers }) {
    return (
        <AppLayout title="Customers" breadcrumbs={[{ label: "Dashboard", url: route('dashboard') }, { label: "Customers" }]}>
            <h1 className="text-xl font-semibold mb-6">Customers</h1>

            <Card>
                <CardContent className="p-0">
                    {customers.length === 0 ? (
                        <p className="text-sm text-muted-foreground p-6">No customers yet.</p>
                    ) : (
                        <table className="w-full text-sm">
                            <thead className="border-b">
                                <tr className="text-muted-foreground text-xs uppercase tracking-wider">
                                    <th className="text-left p-4">Name</th>
                                    <th className="text-left p-4">Email</th>
                                    <th className="text-left p-4">Orders</th>
                                    <th className="text-left p-4">Last Order</th>
                                    <th className="text-left p-4">Joined</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-border">
                                {customers.map(customer => (
                                    <tr key={customer.id} className="hover:bg-muted/30 transition">
                                        <td className="p-4 font-medium">{customer.name}</td>
                                        <td className="p-4 text-muted-foreground">{customer.email}</td>
                                        <td className="p-4">{customer.orders_count}</td>
                                        <td className="p-4 text-muted-foreground">{customer.last_order ?? '—'}</td>
                                        <td className="p-4 text-muted-foreground">{customer.joined}</td>
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
