import AppLayout from '@/components/layout/AppLayout';

export default function AuthenticatedLayout({ header, children }) {
    const title = typeof header === 'object' && header?.props?.children
        ? header.props.children
        : 'Page';

    return (
        <AppLayout title={title}>
            {children}
        </AppLayout>
    );
}
