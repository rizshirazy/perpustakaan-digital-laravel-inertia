import InputError from '@/Components/InputError';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Transition } from '@headlessui/react';
import { useForm } from '@inertiajs/react';
import { useRef } from 'react';

export default function UpdatePasswordForm({ className = '' }) {
    const passwordInput = useRef();
    const currentPasswordInput = useRef();

    const { data, setData, errors, put, reset, processing, recentlySuccessful } = useForm({
        current_password: '',
        password: '',
        password_confirmation: '',
    });

    const onHandleChange = (event) => {
        setData(event.target.name, event.target.type === 'file' ? event.target.files[0] : event.target.value);
    };

    const updatePassword = (e) => {
        e.preventDefault();

        put(route('password.update'), {
            preserveScroll: true,
            onSuccess: () => reset(),
            onError: (errors) => {
                if (errors.password) {
                    reset('password', 'password_confirmation');
                    passwordInput.current.focus();
                }

                if (errors.current_password) {
                    reset('current_password');
                    currentPasswordInput.current.focus();
                }
            },
        });
    };

    return (
        <Card className={className}>
            <CardHeader>
                <CardTitle className="text-lg font-medium text-foreground">Perbarui Password</CardTitle>

                <CardDescription className="mt-1 text-sm text-muted-foreground">
                    Pastikan akun menggunakan Password yang kuat dan acak untuk tetap aman.
                </CardDescription>
            </CardHeader>

            <CardContent>
                <form onSubmit={updatePassword} className="space-y-6">
                    <div className="grid w-full items-center gap-1.5">
                        <Label htmlFor="current_password">Password Saat Ini</Label>

                        <Input
                            id="current_password"
                            name="current_password"
                            ref={currentPasswordInput}
                            value={data.current_password}
                            onChange={onHandleChange}
                            type="password"
                        />

                        {errors.current_password && <InputError message={errors.current_password} className="mt-2" />}
                    </div>

                    <div className="grid w-full items-center gap-1.5">
                        <Label htmlFor="password">Password Baru</Label>

                        <Input
                            id="password"
                            ref={passwordInput}
                            value={data.password}
                            onChange={onHandleChange}
                            type="password"
                            autoComplete="new-password"
                        />

                        {errors.password && <InputError message={errors.password} className="mt-2" />}
                    </div>

                    <div className="grid w-full items-center gap-1.5">
                        <Label htmlFor="password_confirmation">Konfirmasi Password</Label>

                        <Input
                            id="password_confirmation"
                            name="password_confirmation"
                            value={data.password_confirmation}
                            onChange={onHandleChange}
                            type="password"
                            autoComplete="new-password"
                        />

                        {errors.password_confirmation && (
                            <InputError message={errors.password_confirmation} className="mt-2" />
                        )}
                    </div>

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
