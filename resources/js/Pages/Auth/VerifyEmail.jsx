import { Head, Link, useForm } from '@inertiajs/react';
import GuestLayout from '@/Layouts/GuestLayout';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { MailCheck } from 'lucide-react';

export default function VerifyEmail({ status }) {
    const { post, processing } = useForm({});

    const submit = (e) => {
        e.preventDefault();
        post(route('verification.send'));
    };

    return (
        <GuestLayout>
            <Head title="Email Verification" />

            <div className="w-full max-w-sm mx-auto">
                <Card>
                    <CardHeader className="text-center">
                        <div className="flex justify-center mb-2">
                            <div className="flex size-12 items-center justify-center rounded-full bg-muted">
                                <MailCheck className="size-6 text-muted-foreground" />
                            </div>
                        </div>
                        <CardTitle className="text-2xl">Check your email</CardTitle>
                        <CardDescription>
                            Thanks for signing up! Please verify your email address
                            by clicking the link we sent you.
                        </CardDescription>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        {status === 'verification-link-sent' && (
                            <div className="rounded-md bg-green-50 p-3 text-sm font-medium text-green-600">
                                A new verification link has been sent to your email address.
                            </div>
                        )}

                        <form onSubmit={submit}>
                            <Button type="submit" className="w-full" disabled={processing}>
                                {processing ? 'Sending...' : 'Resend verification email'}
                            </Button>
                        </form>

                        <div className="text-center">
                            <Link
                                href={route('logout')}
                                method="post"
                                as="button"
                                className="text-sm text-muted-foreground underline underline-offset-4 hover:text-foreground"
                            >
                                Log out
                            </Link>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </GuestLayout>
    );
}
