import ComboBox from '@/Components/ComboBox';
import HeaderTitle from '@/Components/HeaderTitle';
import InputError from '@/Components/InputError';
import { Button } from '@/Components/ui/button';
import { Card, CardContent } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import AppLayout from '@/Layouts/AppLayout';
import { flashMessage } from '@/lib/utils';
import { Link, useForm } from '@inertiajs/react';
import { IconArrowLeft, IconCreditCardPay } from '@tabler/icons-react';
import { toast } from 'sonner';

export default function Create(props) {
    const { data, setData, reset, post, processing, errors } = useForm({
        user_id: '',
        book_id: '',
        loan_date: props.page_data.loan_date,
        due_date: props.page_data.due_date,
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
                    icon={IconCreditCardPay}
                />

                <Button variant="outline" size="lg" asChild>
                    <Link href={route('admin.loans.index')}>
                        <IconArrowLeft size="4" /> Kembali
                    </Link>
                </Button>
            </div>

            <Card>
                <CardContent className="p-6">
                    <form onSubmit={onHandleSubmit} className="space-y-6">
                        <div className="grid w-full items-center gap-1.5">
                            <Label htmlFor="book_id">Buku</Label>
                            <ComboBox
                                items={props.page_data.books}
                                selectedItem={data.book_id}
                                onSelect={(currentValue) => setData('book_id', currentValue)}
                                placeholder="Pilih Buku"
                            />

                            {errors.book_id && <InputError message={errors.book_id} />}
                        </div>

                        <div className="grid w-full items-center gap-1.5">
                            <Label htmlFor="user_id">Pengguna</Label>
                            <ComboBox
                                items={props.page_data.users}
                                selectedItem={data.user_id}
                                onSelect={(currentValue) => setData('user_id', currentValue)}
                                placeholder="Pilih Pengguna"
                            />

                            {errors.user_id && <InputError message={errors.user_id} />}
                        </div>

                        <div className="grid grid-cols-1 gap-6 lg:grid-cols-2">
                            <div className="grid w-full items-center gap-1.5">
                                <Label htmlFor="loan_date">Tanggal Peminjaman</Label>
                                <Input id="loan_date" value={data.loan_date} disabled />
                            </div>

                            <div className="grid w-full items-center gap-1.5">
                                <Label htmlFor="due_date">Batas Waktu Pengembalian</Label>
                                <Input id="due_date" value={data.due_date} disabled />
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

Create.layout = (page) => <AppLayout title={page.props.page_settings.title}>{page}</AppLayout>;
