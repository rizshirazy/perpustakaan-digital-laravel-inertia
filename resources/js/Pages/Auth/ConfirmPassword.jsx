import InputError from '@/Components/InputError';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import AppLayout from '@/Layouts/AppLayout';
import { useForm } from '@inertiajs/react';

export default function ConfirmPassword() {
    const { data, setData, post, processing, errors, reset } = useForm({
        password: '',
    });

    const onHandleSubmit = (e) => {
        e.preventDefault();

        post(route('password.confirm'), {
            onFinish: () => reset('password'),
        });
    };

    return (
        <Card>
            <CardHeader>
                <CardTitle>Konfirmasi Password</CardTitle>
                <CardDescription>Silakan konfirmasi password Anda sebelum melanjutkan.</CardDescription>
            </CardHeader>

            <CardContent>
                <form onSubmit={onHandleSubmit}>
                    <div className="mt-2">
                        <Label htmlFor="password">Password</Label>

                        <Input
                            id="password"
                            type="password"
                            name="password"
                            value={data.password}
                            className="mt-1 block w-full"
                            onChange={(e) => setData('password', e.target.value)}
                        />

                        {errors.password && <InputError message={errors.password} />}
                    </div>

                    <div className="mt-4 flex items-center justify-end">
                        <Button type="submit" variant="orange" size="xl" disabled={processing}>
                            Konfirmasi
                        </Button>
                    </div>
                </form>
            </CardContent>
        </Card>
    );
}

ConfirmPassword.layout = (page) => <AppLayout title="Konfirmasi Password">{page}</AppLayout>;
