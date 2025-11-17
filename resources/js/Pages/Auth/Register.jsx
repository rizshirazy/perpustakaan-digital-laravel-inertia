import ApplicationLogo from '@/Components/ApplicationLogo';
import InputError from '@/Components/InputError';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import GuestLayout from '@/Layouts/GuestLayout';
import { Link, useForm } from '@inertiajs/react';

export default function Register() {
    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
    });

    const onHandleChange = (e) => {
        setData(e.target.name, e.target.value);
    };

    const onHandleSubmit = (e) => {
        e.preventDefault();

        post(route('register'), {
            onFinish: () => reset('password', 'password_confirmation'),
        });
    };

    return (
        <div className="w-full lg:grid lg:min-h-screen lg:grid-cols-2">
            <div className="flex flex-col px-6 py-4">
                <ApplicationLogo size="size-12" />

                <div className="flex flex-col items-center justify-center py-12 lg:py-24">
                    <div className="mx-auto flex w-full flex-col gap-6 lg:w-1/2">
                        <div className="grid gap-2 text-center">
                            <h1 className="text-3xl font-bold">Daftar</h1>
                            <p className="mb-3 text-balance text-muted-foreground">
                                Masukkan informasi Anda untuk membuat akun baru
                            </p>
                        </div>

                        <form onSubmit={onHandleSubmit}>
                            <div className="grid gap-4">
                                <div className="grid gap-2">
                                    <Label htmlFor="name">Nama</Label>

                                    <Input
                                        id="name"
                                        type="text"
                                        name="name"
                                        value={data.name}
                                        className="mt-1 block w-full"
                                        autoComplete="name"
                                        onChange={onHandleChange}
                                    />

                                    {errors.name && <InputError message={errors.name} />}
                                </div>

                                <div className="grid gap-2">
                                    <Label htmlFor="email">Email</Label>

                                    <Input
                                        id="email"
                                        type="email"
                                        name="email"
                                        value={data.email}
                                        className="mt-1 block w-full"
                                        autoComplete="email"
                                        onChange={onHandleChange}
                                    />

                                    {errors.email && <InputError message={errors.email} />}
                                </div>

                                <div className="grid gap-2">
                                    <div className="flex items-center">
                                        <Label htmlFor="password">Password</Label>
                                    </div>

                                    <Input
                                        id="password"
                                        type="password"
                                        name="password"
                                        value={data.password}
                                        className="mt-1 block w-full"
                                        autoComplete="new-password"
                                        onChange={onHandleChange}
                                    />

                                    {errors.password && <InputError message={errors.password} />}
                                </div>

                                <div className="grid gap-2">
                                    <div className="flex items-center">
                                        <Label htmlFor="password_confirmation">Konfirmasi Password</Label>
                                    </div>

                                    <Input
                                        id="password_confirmation"
                                        type="password"
                                        name="password_confirmation"
                                        value={data.password_confirmation}
                                        className="mt-1 block w-full"
                                        autoComplete="new-password"
                                        onChange={onHandleChange}
                                    />

                                    {errors.password_confirmation && (
                                        <InputError message={errors.password_confirmation} />
                                    )}
                                </div>

                                <Button
                                    type="submit"
                                    size="xl"
                                    variant="orange"
                                    className="mt-4 w-full"
                                    disabled={processing}
                                >
                                    Daftar
                                </Button>
                            </div>
                        </form>
                        <div className="mt-4 text-center text-sm">
                            Sudah punya akun? {''}{' '}
                            <Link href={route('login')} className="underline">
                                Login
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

Register.layout = (page) => <GuestLayout title="Daftar">{page}</GuestLayout>;
