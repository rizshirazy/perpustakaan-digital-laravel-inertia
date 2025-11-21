import InputError from '@/Components/InputError';
import { Alert, AlertDescription } from '@/Components/ui/alert';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Transition } from '@headlessui/react';
import { Link, useForm, usePage } from '@inertiajs/react';

export default function UpdateProfileInformation({ mustVerifyEmail, status, className = '' }) {
    const user = usePage().props.auth.user;

    const { data, setData, patch, errors, processing, recentlySuccessful } = useForm({
        name: user.name,
        email: user.email,
    });

    const onHandleChange = (event) => {
        setData(event.target.name, event.target.type === 'file' ? event.target.files[0] : event.target.value);
    };

    const onHandleSubmit = (e) => {
        e.preventDefault();

        patch(route('profile.update'));
    };

    return (
        <Card className={className}>
            <CardHeader>
                <CardTitle className="text-lg font-medium text-gray-900">Informasi Profil</CardTitle>

                <CardDescription className="mt-1 text-sm text-muted-foreground">
                    Perbarui informasi profil dan alamat email akun Anda.
                </CardDescription>
            </CardHeader>

            <CardContent>
                <form onSubmit={onHandleSubmit} className="space-y-6">
                    <div className="grid w-full items-center gap-1.5">
                        <Label htmlFor="name">Nama</Label>

                        <Input id="name" name="name" value={data.name} onChange={onHandleChange} autoComplete="name" />

                        {errors.name && <InputError className="mt-2" message={errors.name} />}
                    </div>

                    <div className="grid w-full items-center gap-1.5">
                        <Label htmlFor="email">Email</Label>

                        <Input
                            id="email"
                            name="email"
                            type="email"
                            value={data.email}
                            onChange={onHandleChange}
                            autoComplete="username"
                        />

                        {errors.email && <InputError className="mt-2" message={errors.email} />}
                    </div>

                    {mustVerifyEmail && user.email_verified_at === null && (
                        <div className="grid w-full items-center gap-1.5">
                            <p className="mt-2 text-sm text-foreground">
                                Alamat email Anda belum terverifikasi.
                                <Link
                                    href={route('verification.send')}
                                    method="post"
                                    as="button"
                                    className="rounded-md text-sm text-muted-foreground underline hover:text-foreground focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2"
                                >
                                    Klik di sini untuk mengirim ulang email verifikasi.
                                </Link>
                            </p>

                            {status === 'verification-link-sent' && (
                                <Alert variant="success">
                                    <AlertDescription>
                                        Tautan verifikasi baru telah dikirim ke alamat email Anda.
                                    </AlertDescription>
                                </Alert>
                            )}
                        </div>
                    )}

                    <div className="flex items-center gap-4">
                        <Button variant="orange" size="lg" disabled={processing}>
                            Simpan
                        </Button>

                        <Transition
                            show={recentlySuccessful}
                            enter="transition ease-in-out"
                            enterFrom="opacity-0"
                            leave="transition ease-in-out"
                            leaveTo="opacity-0"
                        >
                            <p className="text-sm text-muted-foreground">Tersimpan.</p>
                        </Transition>
                    </div>
                </form>
            </CardContent>
        </Card>
    );
}
