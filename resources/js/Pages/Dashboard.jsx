import AppLayout from "@/components/layout/AppLayout";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Package, ShoppingBag, Users, CreditCard } from "lucide-react";
import { usePage } from '@inertiajs/react';

const stats = [
    { title: "Total Orders", value: "0", icon: Package, change: "No orders yet" },
    { title: "Revenue", value: "RM 0.00", icon: CreditCard, change: "No payments yet" },
    { title: "Products", value: "0", icon: ShoppingBag, change: "No products yet" },
    { title: "Customers", value: "0", icon: Users, change: "No customers yet" },
];

export default function Dashboard() {
    const { auth } = usePage().props;
    const user = auth.user;

    return (
        <AppLayout
            title="Dashboard"
            breadcrumbs={[{ label: "Dashboard", url: route('dashboard') }]}
        >
            <div className="pb-6">
                <p className="text-2xl font-bold">Welcome, {user.name}!</p>
            </div>

            <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                {stats.map((stat) => (
                    <Card key={stat.title}>
                        <CardHeader className="flex flex-row items-center justify-between pb-2">
                            <CardTitle className="text-sm font-medium text-muted-foreground">
                                {stat.title}
                            </CardTitle>
                            <stat.icon className="size-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{stat.value}</div>
                            <p className="text-xs text-muted-foreground mt-1">{stat.change}</p>
                        </CardContent>
                    </Card>
                ))}
            </div>

            <div className="grid gap-4 md:grid-cols-2">
                <Card>
                    <CardHeader>
                        <CardTitle>Recent Orders</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p className="text-sm text-muted-foreground">No orders yet.</p>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader>
                        <CardTitle>Recent Payments</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p className="text-sm text-muted-foreground">No payments yet.</p>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
