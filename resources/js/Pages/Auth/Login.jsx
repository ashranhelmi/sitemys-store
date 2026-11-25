import { Head, Link, useForm } from '@inertiajs/react';
import GuestLayout from '@/Layouts/GuestLayout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
import { Card, CardContent } from '@/components/ui/card';
import Wordmark from '@/Components/Wordmark';

export default function Login({ status, canResetPassword }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    const submit = (e) => {
        e.preventDefault();
        post(route('login'), {
            onFinish: () => reset('password'),
        });
    };

    return (
        <GuestLayout>
            <Head title="Log in" />

            <Card className="overflow-hidden p-0">
                <CardContent className="grid p-0 md:grid-cols-2">

                    {/* Left — form */}
                    <form onSubmit={submit} className="p-6 md:p-8 space-y-4">
                        <div className="flex flex-col items-center gap-3 text-center mb-2">
                            <Wordmark />
                            <h1 className="text-2xl font-bold tracking-tight">Store admin</h1>
                            <p className="text-sm text-muted-foreground text-balance">
                                Sign in to manage products, orders and payments.
                            </p>
                        </div>

                        {status && (
                            <div className="text-sm font-medium text-green-600">
                                {status}
                            </div>
                        )}

                        <div className="space-y-2">
                            <Label htmlFor="email">Email</Label>
                            <Input
                                id="email"
                                type="email"
                                value={data.email}
                                autoComplete="username"
                                autoFocus
                                onChange={(e) => setData('email', e.target.value)}
                                placeholder="m@example.com"
                            />
                            {errors.email && (
                                <p className="text-sm text-destructive">{errors.email}</p>
                            )}
                        </div>

                        <div className="space-y-2">
                            <div className="flex items-center justify-between">
                                <Label htmlFor="password">Password</Label>
                                {canResetPassword && (
                                    <Link
                                        href={route('password.request')}
                                        className="text-sm underline-offset-2 hover:underline text-muted-foreground"
                                    >
                                        Forgot your password?
                                    </Link>
                                )}
                            </div>
                            <Input
                                id="password"
                                type="password"
                                value={data.password}
                                autoComplete="current-password"
                                onChange={(e) => setData('password', e.target.value)}
                                placeholder="••••••••"
                            />
                            {errors.password && (
                                <p className="text-sm text-destructive">{errors.password}</p>
                            )}
                        </div>

                        <div className="flex items-center gap-2">
                            <Checkbox
                                id="remember"
                                checked={data.remember}
                                onCheckedChange={(checked) => setData('remember', checked)}
                            />
                            <Label htmlFor="remember" className="font-normal cursor-pointer">
                                Remember me
                            </Label>
                        </div>

                        <Button type="submit" className="w-full" disabled={processing}>
                            {processing ? 'Signing in...' : 'Login'}
                        </Button>

                        <p className="text-center text-sm text-muted-foreground">
                            <a href="/" className="text-foreground font-medium underline underline-offset-4 hover:opacity-80">Back to the store</a>
                        </p>
                    </form>

                    {/* Right — image */}
                    <div className="relative hidden bg-muted md:block">
                        <img
                            src="/images/looks/look-monsoon.jpg"
                            alt="Look 02, Monsoon"
                            className="absolute inset-0 h-full w-full object-cover object-[74%_50%] dark:brightness-75"
                        />
                    </div>

                </CardContent>
            </Card>

            <p className="mt-4 px-6 text-center text-xs text-muted-foreground">
                Admin accounts are created with <code>php artisan store:admin</code>.
            </p>
        </GuestLayout>
    );
}
