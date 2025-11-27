import HeaderTitle from '@/Components/HeaderTitle';
import InputError from '@/Components/InputError';
import { MultiSelect } from '@/Components/MultiSelect';
import { Button } from '@/Components/ui/button';
import { Card, CardContent } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import AppLayout from '@/Layouts/AppLayout';
import { flashMessage } from '@/lib/utils';
import { Link, useForm } from '@inertiajs/react';
import { IconArrowLeft, IconKeyframe } from '@tabler/icons-react';

import { toast } from 'sonner';

export default function Edit(props) {
    const { data: user, roles } = props.page_data;

    const { data, setData, reset, post, processing, errors } = useForm({
        name: user.name ?? '',
        roles: user.roles ?? [],
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
                    icon={IconKeyframe}
                />

                <Button variant="outline" size="lg" asChild>
                    <Link href={route('admin.assign-users.index')}>
                        <IconArrowLeft size="4" /> Kembali
                    </Link>
                </Button>
            </div>

            <Card>
                <CardContent className="p-6">
                    <form onSubmit={onHandleSubmit} className="space-y-6">
                        <div className="grid w-full items-center gap-1.5">
                            <Label htmlFor="name">Nama</Label>
                            <Input id="name" name="name" type="text" value={data.name} disabled />
                            {errors.name && <InputError message={errors.name} />}
                        </div>

                        <div className="grid w-full items-center gap-1.5">
                            <Label htmlFor="roles">Peran</Label>
                            <MultiSelect
                                id="roles"
                                name="roles"
                                options={roles}
                                defaultValue={data.roles}
                                placeholder="Pilih peran"
                                variant="inverted"
                                onValueChange={(selected) => setData('roles', selected)}
                            />
                            {errors.roles && <InputError message={errors.roles} />}
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
