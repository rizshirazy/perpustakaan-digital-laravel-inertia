import ApplicationLogo from '@/Components/ApplicationLogo';
import { Alert, AlertDescription } from '@/Components/ui/alert';
import { Button } from '@/Components/ui/button';
import GuestLayout from '@/Layouts/GuestLayout';
import { Link, useForm } from '@inertiajs/react';

export default function VerifyEmail({ status }) {
    const { post, processing } = useForm({});

    const onHandleSubmit = (e) => {
        e.preventDefault();

        post(route('verification.send'));
    };

    return (
        <div className="w-full lg:grid lg:min-h-screen lg:grid-cols-2">
            <div className="flex flex-col px-6 py-4">
                <ApplicationLogo size="size-12" />

                <div className="flex flex-col items-center justify-center py-12 lg:py-48">
                    <div className="mx-auto flex w-full flex-col gap-6 lg:w-1/2">
                        <div className="grid gap-2 text-center">
                            {status === 'verification-link-sent' && (
                                <Alert variant="success">
                                    <AlertDescription>
                                        Link verifikasi baru telah dikirim ke alamat email yang Anda berikan saat
                                        pendaftaran.
                                    </AlertDescription>
                                </Alert>
                            )}

                            <h1 className="text-3xl font-bold">Verifikasi Email</h1>

                            <p className="my-6 text-balance text-muted-foreground">
                                Terima kasih telah mendaftar! Sebelum memulai, bisakah Anda memverifikasi alamat email
                                Anda dengan mengklik tautan yang baru saja kami kirimkan kepada Anda? Jika Anda tidak
                                menerima emailnya, kami dengan senang hati akan mengirimkan yang lain.
                            </p>
                        </div>
                        <form onSubmit={onHandleSubmit}>
                            <div className="grid gap-4">
                                <Button
                                    type="submit"
                                    size="xl"
                                    variant="orange"
                                    className="w-full"
                                    disabled={processing}
                                >
                                    Kirim Ulang Link Verifikasi Email
                                </Button>
                            </div>
                        </form>
                        <div className="mt-4 text-center text-sm">
                            <Link href={route('logout')} method="post" alias="button" className="underline">
                                Logout
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <div className="hidden bg-muted lg:flex">
                <img
                    src="/images/login.webp"
                    alt="Login"
                    className="h-full w-full object-cover dark:brightness-[0.4] dark:grayscale"
                />
            </div>
        </div>
    );
}

VerifyEmail.layout = (page) => <GuestLayout title="Verifikasi Email">{page}</GuestLayout>;
