import * as React from "react"
import {
    LayoutDashboard,
    Package,
    ShoppingBag,
    Users,
    CreditCard,
} from "lucide-react"
import { NavMain } from "@/components/nav-main"
import { NavUser } from "@/components/nav-user"
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuItem,
    SidebarMenuButton,
    SidebarRail,
} from "@/components/ui/sidebar"

const data = {
    navMain: [
        {
            title: "Dashboard",
            url: "/dashboard",
            icon: LayoutDashboard,
        },
        {
            title: "Orders",
            url: "/dashboard/admin/orders",
            icon: Package,
        },
        {
            title: "Products",
            url: "/dashboard/admin/products",
            icon: ShoppingBag,
        },
        {
            title: "Customers",
            url: "/dashboard/admin/customers",
            icon: Users,
        },
        {
            title: "Payments",
            url: "/dashboard/admin/payments",
            icon: CreditCard,
        },
    ],
}

export function AppSidebar({ ...props }) {
    return (
        <Sidebar collapsible="icon" {...props}>
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <a href="/dashboard">
                                {/* sitemys. letter mark, no background square (DHelm, 25 Nov) */}
                                <div className="flex aspect-square size-8 items-center justify-center">
                                    <img src="/images/sitemys-s-mark-ink.svg" alt="" className="h-6 w-auto dark:hidden" />
                                    <img src="/images/sitemys-s-mark-white.svg" alt="" className="h-6 w-auto hidden dark:block" />
                                </div>
                                <div className="flex flex-col gap-0.5 leading-none">
                                    <span className="font-semibold">Sitemys Store</span>
                                    <span className="text-xs text-muted-foreground">Admin</span>
                                </div>
                            </a>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>
            <SidebarContent>
                <NavMain items={data.navMain} />
            </SidebarContent>
            <SidebarFooter>
                <NavUser />
            </SidebarFooter>
            <SidebarRail />
        </Sidebar>
    )
}
