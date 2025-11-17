import HeaderTitle from '@/Components/HeaderTitle';
import InputError from '@/Components/InputError';
import { Button } from '@/Components/ui/button';
import { Card, CardContent } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';
import { Textarea } from '@/Components/ui/textarea';
import AppLayout from '@/Layouts/AppLayout';
import { flashMessage } from '@/lib/utils';
import { Link, useForm } from '@inertiajs/react';
import { IconArrowLeft, IconBuildingCommunity } from '@tabler/icons-react';
import { toast } from 'sonner';

export default function Create(props) {
    const { name, address, phone, email } = props.page_data.data;

    const { data, setData, reset, post, processing, errors } = useForm({
        name: name || '',
        password: '',
        password_confirmation: '',
        address: address || '',
        phone: phone || '',
        email: email || '',
        gender: '',
        date_of_birth: '',
        avatar: null,
        _method: props.page_settings.method,
    });

    const onHandleChange = (event) => {
        setData(event.target.name, event.target.type === 'file' ? event.target.files[0] : event.target.value);
    };

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
                    icon={IconBuildingCommunity}
                />

                <Button variant="outline" size="lg" asChild>
                    <Link href={route('admin.users.index')}>
                        <IconArrowLeft size="4" /> Kembali
                    </Link>
                </Button>
            </div>

            <Card>
                <CardContent className="p-6">
                    <form onSubmit={onHandleSubmit} className="space-y-6">
                        <div className="grid w-full items-center gap-1.5">
                            <Label htmlFor="name">Nama</Label>
                            <Input
                                id="name"
                                name="name"
                                type="text"
                                placeholder="Masukkan nama pengguna"
                                value={data.name}
                                onChange={onHandleChange}
                            />
                            {errors.name && <InputError message={errors.name} />}
                        </div>

                        <div className="grid grid-cols-1 gap-6 lg:grid-cols-2">
                            <div className="grid w-full items-center gap-1.5">
                                <Label htmlFor="email">Email</Label>
                                <Input
                                    id="email"
                                    name="email"
                                    type="email"
                                    placeholder="Masukkan email pengguna"
                                    value={data.email}
                                    onChange={onHandleChange}
                                />
                                {errors.email && <InputError message={errors.email} />}
                            </div>

                            <div className="grid w-full items-center gap-1.5">
                                <Label htmlFor="phone">Nomor Telepon</Label>
                                <Input
                                    id="phone"
                                    name="phone"
                                    type="text"
                                    placeholder="Masukkan nomor telepon pengguna"
                                    value={data.phone}
                                    onChange={onHandleChange}
                                />
                                {errors.phone && <InputError message={errors.phone} />}
                            </div>
                        </div>

                        <div className="grid grid-cols-1 gap-6 lg:grid-cols-2">
                            <div className="grid w-full items-center gap-1.5">
                                <Label htmlFor="date_of_birth">Tanggal Lahir</Label>
                                <Input
                                    id="date_of_birth"
                                    name="date_of_birth"
                                    type="date"
                                    value={data.date_of_birth}
                                    onChange={onHandleChange}
                                />
                                {errors.date_of_birth && <InputError message={errors.date_of_birth} />}
                            </div>

                            <div className="grid w-full items-center gap-1.5">
                                <Label htmlFor="language">Jenis Kelamin</Label>
                                <Select value={data.gender} onValueChange={(value) => setData('gender', value)}>
                                    <SelectTrigger className="w-full">
                                        <SelectValue placeholder="Pilih Jenis Kelamin" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {props.page_data.genders?.map((gender, index) => (
                                            <SelectItem key={index} value={gender.value}>
                                                {gender.label}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>

                                {errors.gender && <InputError message={errors.gender} />}
                            </div>
                        </div>

                        <div className="grid w-full items-center gap-1.5">
                            <Label htmlFor="address">Alamat</Label>
                            <Textarea
                                id="address"
                                name="address"
                                placeholder="Isi alamat pengguna"
                                value={data.address}
                                onChange={onHandleChange}
                            />
                            {errors.address && <InputError message={errors.address} />}
                        </div>

                        <div className="grid grid-cols-1 gap-6 lg:grid-cols-2">
                            <div className="grid w-full items-center gap-1.5">
                                <Label htmlFor="password">Password</Label>
                                <Input
                                    id="password"
                                    name="password"
                                    type="password"
                                    placeholder="Masukkan password pengguna"
                                    value={data.password}
                                    onChange={onHandleChange}
                                />
                                {errors.password && <InputError message={errors.password} />}
                            </div>

                            <div className="grid w-full items-center gap-1.5">
                                <Label htmlFor="password_confirmation">Konfirmasi Password</Label>
                                <Input
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    type="password"
                                    placeholder="Masukkan kembali password pengguna"
                                    value={data.password_confirmation}
                                    onChange={onHandleChange}
                                />
                                {errors.password_confirmation && <InputError message={errors.password_confirmation} />}
                            </div>
                        </div>

                        <div className="grid w-full items-center gap-1.5">
                            <Label htmlFor="avatar">Avatar</Label>
                            <Input id="avatar" name="avatar" type="file" onChange={onHandleChange} />
                            {errors.avatar && <InputError message={errors.avatar} />}
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

Create.layout = (page) => <AppLayout title={page.props.page_settings.title}>{page}</AppLayout>;
