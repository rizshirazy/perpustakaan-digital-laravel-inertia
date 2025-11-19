import HeaderTitle from '@/Components/HeaderTitle';
import InputError from '@/Components/InputError';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';
import { Textarea } from '@/Components/ui/textarea';
import AppLayout from '@/Layouts/AppLayout';
import { flashMessage } from '@/lib/utils';
import { Link, useForm } from '@inertiajs/react';
import { IconArrowLeft, IconCreditCardRefund } from '@tabler/icons-react';
import { toast } from 'sonner';

export default function Create(props) {
    const { data: loan } = props.page_data;

    const { data, setData, reset, post, processing, errors } = useForm({
        loan_code: loan.loan_code,
        loan_date: loan.loan_date.raw,
        due_date: loan.due_date.raw,
        condition: '',
        notes: '',
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
        <div className="flex w-full flex-col space-y-4 pb-32 lg:space-y-8">
            <div className="mb-8 flex flex-col items-start justify-between gap-y-4 lg:flex-row lg:items-center">
                <HeaderTitle
                    title={props.page_settings.title}
                    subtitle={props.page_settings.subtitle}
                    icon={IconCreditCardRefund}
                />
                {console.log(props.page_data.data.book.publisher)}

                <Button variant="outline" size="lg" asChild>
                    <Link href={route('admin.return-books.index')}>
                        <IconArrowLeft size="4" /> Kembali
                    </Link>
                </Button>
            </div>

            <div className="grid gap-4 lg:grid-cols-2 lg:gap-8">
                <Card>
                    <CardHeader>
                        <CardTitle>Data Peminjam</CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        <div className="grid w-full items-center gap-1.5">
                            <Label>Nama</Label>
                            <Input value={loan.user.name} type="text" disabled />
                        </div>

                        <div className="grid w-full items-center gap-1.5">
                            <Label>Username</Label>
                            <Input value={loan.user.username} type="text" disabled />
                        </div>

                        <div className="grid w-full items-center gap-1.5">
                            <Label>Email</Label>
                            <Input value={loan.user.email} type="text" disabled />
                        </div>

                        <div className="grid w-full items-center gap-1.5">
                            <Label>Nomor Telepon</Label>
                            <Input value={loan.user.phone} type="text" disabled />
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Data Buku</CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        <div className="grid w-full items-center gap-1.5">
                            <Label>Kode Buku</Label>
                            <Input value={loan.book.book_code} type="text" disabled />
                        </div>

                        <div className="grid w-full items-center gap-1.5">
                            <Label>Judul Buku</Label>
                            <Input value={loan.book.title} type="text" disabled />
                        </div>

                        <div className="grid w-full items-center gap-1.5">
                            <Label>Penulis</Label>
                            <Input value={loan.book.author} type="text" disabled />
                        </div>

                        <div className="grid w-full items-center gap-1.5">
                            <Label>Penerbit</Label>
                            <Input value={loan.book.publisher?.name} type="text" disabled />
                        </div>
                    </CardContent>
                </Card>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Data Peminjaman</CardTitle>
                </CardHeader>
                <CardContent>
                    <form className="space-y-6" onSubmit={onHandleSubmit}>
                        <div className="grid grid-cols-1 gap-6 lg:grid-cols-4">
                            <div className="grid w-full items-center gap-1.5">
                                <Label htmlFor="loan_code">Kode Peminjaman</Label>
                                <Input id="loan_code" name="loan_code" type="text" value={data.loan_code} disabled />
                                {errors.loan_code && <InputError message={errors.loan_code} />}
                            </div>

                            <div className="grid w-full items-center gap-1.5">
                                <Label htmlFor="loan_date">Tanggal Peminjaman</Label>
                                <Input id="loan_date" name="loan_date" type="date" value={data.loan_date} disabled />
                                {errors.loan_date && <InputError message={errors.loan_date} />}
                            </div>

                            <div className="grid w-full items-center gap-1.5">
                                <Label htmlFor="due_date">Batas Pengembalian</Label>
                                <Input id="due_date" name="due_date" type="date" value={data.due_date} disabled />
                                {errors.due_date && <InputError message={errors.due_date} />}
                            </div>

                            <div className="grid w-full items-center gap-1.5">
                                <Label htmlFor="condition">Kondisi</Label>
                                <Select value={data.condition} onValueChange={(value) => setData('condition', value)}>
                                    <SelectTrigger className="w-full">
                                        <SelectValue placeholder="Pilih Kondisi" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {props.page_data.conditions?.map((condition, index) => (
                                            <SelectItem key={index} value={condition.value}>
                                                {condition.label}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                                {errors.condition && <InputError message={errors.condition} />}
                            </div>
                        </div>

                        <div className="grid w-full items-center gap-1.5">
                            <Label htmlFor="notes">Catatan</Label>
                            <Textarea id="notes" name="notes" value={data.notes} onChange={onHandleChange}></Textarea>
                            {errors.notes && <InputError message={errors.notes} />}
                        </div>

                        <div className="flex justify-end gap-x-2">
                            <Button type="button" variant="ghost" size="lg" onClick={() => reset()}>
                                Reset
                            </Button>
                            <Button type="submit" variant="orange" size="lg" disabled={processing}>
                                Kembalikan
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    );
}

Create.layout = (page) => <AppLayout title={page.props.page_settings.title}>{page}</AppLayout>;
