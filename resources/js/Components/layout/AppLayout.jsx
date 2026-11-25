import { Head } from "@inertiajs/react";
import { SidebarInset, SidebarProvider } from "@/components/ui/sidebar";
import { AppSidebar } from "@/components/app-sidebar";
import AppHeader from "@/components/layout/AppHeader";
import { useFlash } from "@/hooks/useFlash";

export default function AppLayout({ title, children, breadcrumbs = [] }) {
    useFlash()

    return (
        <>
            <Head title={title} />
            <SidebarProvider>
                <AppSidebar />
                <SidebarInset>
                    <AppHeader breadcrumbs={breadcrumbs} />
                    <main className="flex flex-1 flex-col gap-4 p-6">
                        {children}
                    </main>
                </SidebarInset>
            </SidebarProvider>
        </>
    );
}
