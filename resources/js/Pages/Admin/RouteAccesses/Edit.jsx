import ComboBox from '@/Components/ComboBox';
import HeaderTitle from '@/Components/HeaderTitle';
import InputError from '@/Components/InputError';
import { Button } from '@/Components/ui/button';
import { Card, CardContent } from '@/Components/ui/card';
import { Label } from '@/Components/ui/label';
import AppLayout from '@/Layouts/AppLayout';
import { flashMessage } from '@/lib/utils';
import { Link, useForm } from '@inertiajs/react';
import { IconArrowLeft, IconRoute } from '@tabler/icons-react';
import { toast } from 'sonner';

export default function Edit(props) {
    const { data: route_access, roles, permissions, routes } = props.page_data;

    const { data, setData, reset, post, processing, errors } = useForm({
        route_name: route_access.route_name || '',
        role_id: route_access.role?.id || '',
        permission_id: route_access.permission?.id || '',
        cover: null,
        _method: props.page_settings.method,
    });

    const onHandleSubmit = (e) => {
        e.preventDefault();

        post(props.page_settings.action, {
            preserveScroll: true,
            preserveState: true,
            onSuccess: (success) => {
                const flash = flashMessage(success);

                if (flash) toast[flash.type](flash.message);
            },
        });
    };

    return (
        <div className="flex w-full flex-col pb-32">
            <div className="mb-8 flex flex-col items-start justify-between gap-y-4 lg:flex-row lg:items-center">
                <HeaderTitle
                    title={props.page_settings.title}
                    subtitle={props.page_settings.subtitle}
                    icon={IconRoute}
                />

                <Button variant="outline" size="lg" asChild>
                    <Link href={route('admin.route-accesses.index')}>
                        <IconArrowLeft size="4" /> Kembali
                    </Link>
                </Button>
            </div>

            <Card>
                <CardContent className="p-6">
                    <form onSubmit={onHandleSubmit} className="space-y-6">
                        <div className="grid w-full items-center gap-1.5">
                            <Label htmlFor="route_name">Rute</Label>
                            <ComboBox
                                id="route_name"
                                items={routes}
                                selectedItem={data.route_name}
                                onSelect={(value) => setData('route_name', value)}
                                placeholder="Pilih Rute"
                            />
                            {errors.route_name && <InputError message={errors.route_name} />}
                        </div>

                        <div className="grid grid-cols-1 gap-6 lg:grid-cols-2">
                            <div className="grid w-full items-center gap-1.5">
                                <Label htmlFor="role_id">Peran</Label>
                                <ComboBox
                                    id="role_id"
                                    items={roles}
                                    selectedItem={data.role_id}
                                    onSelect={(value) => setData('role_id', value)}
                                    placeholder="Pilih Peran"
                                />
                                {errors.role_id && <InputError message={errors.role_id} />}
                            </div>

                            <div className="grid w-full items-center gap-1.5">
                                <Label htmlFor="permission_id">Izin</Label>
                                <ComboBox
                                    id="permission_id"
                                    items={permissions}
                                    selectedItem={data.permission_id}
                                    onSelect={(value) => setData('permission_id', value)}
                                    placeholder="Pilih Izin"
                                />
                                {errors.permission_id && <InputError message={errors.permission_id} />}
                            </div>
                        </div>

                        <div className="flex justify-end gap-x-2">
                            <Button type="button" variant="ghost" size="lg" onClick={() => reset()}>
                                Reset
                            </Button>
                            <Button type="submit" variant="orange" size="lg" disabled={processing}>
                                Simpan
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    );
}

Edit.layout = (page) => <AppLayout title={page.props.page_settings.title}>{page}</AppLayout>;
