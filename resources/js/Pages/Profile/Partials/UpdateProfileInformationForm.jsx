import { Link, useForm, usePage } from '@inertiajs/react';
import { Transition } from '@headlessui/react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

export default function UpdateProfileInformation({ mustVerifyEmail, status }) {
    const user = usePage().props.auth.user;

    const { data, setData, patch, errors, processing, recentlySuccessful } = useForm({
        name: user.name,
        email: user.email,
    });

    const submit = (e) => {
        e.preventDefault();
        patch(route('profile.update'));
    };

    return (
        <form onSubmit={submit} className="space-y-4">
            <div className="space-y-2">
                <Label htmlFor="name">Name</Label>
                <Input
                    id="name"
                    value={data.name}
                    onChange={(e) => setData('name', e.target.value)}
                    required
                    autoFocus
                    autoComplete="name"
                />
                {errors.name && <p className="text-sm text-destructive">{errors.name}</p>}
            </div>

            <div className="space-y-2">
                <Label htmlFor="email">Email</Label>
                <Input
                    id="email"
                    type="email"
                    value={data.email}
                    onChange={(e) => setData('email', e.target.value)}
                    required
                    autoComplete="username"
                />
                {errors.email && <p className="text-sm text-destructive">{errors.email}</p>}
            </div>

            {mustVerifyEmail && user.email_verified_at === null && (
                <div className="rounded-md bg-muted p-4 text-sm">
                    <p className="text-muted-foreground">
                        Your email address is unverified.{' '}
                        <Link
                            href={route('verification.send')}
                            method="post"
                            as="button"
                            className="underline underline-offset-4 hover:text-foreground"
                        >
                            Click here to re-send the verification email.
                        </Link>
                    </p>
                    {status === 'verification-link-sent' && (
                        <p className="mt-2 font-medium text-green-600">
                            A new verification link has been sent to your email address.
                        </p>
                    )}
                </div>
            )}

            <div className="flex items-center gap-4 pt-2">
                <Button type="submit" disabled={processing}>
                    {processing ? 'Saving...' : 'Save changes'}
                </Button>
                <Transition
                    show={recentlySuccessful}
                    enter="transition ease-in-out"
                    enterFrom="opacity-0"
                    leave="transition ease-in-out"
                    leaveTo="opacity-0"
                >
                    <p className="text-sm text-muted-foreground">Saved.</p>
                </Transition>
            </div>
        </form>
    );
}
