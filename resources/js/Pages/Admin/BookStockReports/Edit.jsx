import HeaderTitle from '@/Components/HeaderTitle';
import InputError from '@/Components/InputError';
import { Button } from '@/Components/ui/button';
import { Card, CardContent } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import AppLayout from '@/Layouts/AppLayout';
import { flashMessage } from '@/lib/utils';
import { Link, useForm } from '@inertiajs/react';
import { IconArrowLeft, IconStack3 } from '@tabler/icons-react';
import { toast } from 'sonner';

export default function Edit(props) {
    const { book, total, available, lost, damaged, loaned } = props.stock;

    const { data, setData, reset, post, processing, errors } = useForm({
        book: book.title || '',
        total: total || 0,
        available: available || 0,
        lost: lost || 0,
        damaged: damaged || 0,
        loaned: loaned || 0,
        _method: props.page_settings.method,
    });

    const calculateMinimumTotal = (lost, damaged, loaned) => {
        return available + lost + damaged + loaned;
    };

    const onHandleChange = (event) => {
        const { name, value } = event.target;

        let updatedData = { ...data, [name]: value };

        if (name === 'total') {
            updatedData.total = parseInt(value) || 0;
        }

        const minTotal = calculateMinimumTotal(
            parseInt(updatedData.lost) || 0,
            parseInt(updatedData.damaged) || 0,
            parseInt(updatedData.loaned) || 0,
        );

        if (name === 'total' && updatedData.total < minTotal) {
            updatedData.total = minTotal;
        }

        let totalDiff = (parseInt(updatedData.total) || 0) - (parseInt(data.total) || 0);

        updatedData.available = Math.max(0, (parseInt(updatedData.available) || 0) + totalDiff);

        setData(updatedData);
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
                    icon={IconStack3}
                />

                <Button variant="outline" size="lg" asChild>
                    <Link href={route('admin.book-stock-reports.index')}>
                        <IconArrowLeft size="4" /> Kembali
                    </Link>
                </Button>
            </div>

            <Card>
                <CardContent className="p-6">
                    <form onSubmit={onHandleSubmit} className="space-y-6">
                        <div className="grid w-full items-center gap-1.5">
                            <Label htmlFor="book">Judul Buku</Label>
                            <Input id="book" name="book" type="text" value={data.book} disabled />
                            {errors.book && <InputError message={errors.book} />}
                        </div>

                        <div className="grid grid-cols-1 gap-6 lg:grid-cols-2">
                            <div className="grid w-full items-center gap-1.5">
                                <Label htmlFor="total">Total Stock</Label>
                                <Input
                                    id="total"
                                    name="total"
                                    type="number"
                                    value={data.total}
                                    onChange={onHandleChange}
                                />
                                {errors.total && <InputError message={errors.total} />}
                            </div>

                            <div className="grid w-full items-center gap-1.5">
                                <Label htmlFor="available">Jumlah Tersedia</Label>
                                <Input id="available" name="available" type="number" value={data.available} disabled />
                                {errors.available && <InputError message={errors.available} />}
                            </div>
                        </div>

                        <div className="grid grid-cols-1 gap-6 lg:grid-cols-3">
                            <div className="grid w-full items-center gap-1.5">
                                <Label htmlFor="loaned">Jumlah Dimpinjam</Label>
                                <Input id="loaned" name="loaned" type="number" value={data.loaned} disabled />
                                {errors.loaned && <InputError message={errors.loaned} />}
                            </div>

                            <div className="grid w-full items-center gap-1.5">
                                <Label htmlFor="damaged">Jumlah Rusak</Label>
                                <Input id="damaged" name="damaged" type="number" value={data.damaged} disabled />
                                {errors.damaged && <InputError message={errors.damaged} />}
                            </div>

                            <div className="grid w-full items-center gap-1.5">
                                <Label htmlFor="lost">Jumlah Hilang</Label>
                                <Input id="lost" name="lost" type="number" value={data.lost} disabled />
                                {errors.lost && <InputError message={errors.lost} />}
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
