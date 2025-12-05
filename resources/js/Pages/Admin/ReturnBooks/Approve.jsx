import InputError from '@/Components/InputError';
import { Button } from '@/Components/ui/button';
import { Card, CardContent } from '@/Components/ui/card';
import { Label } from '@/Components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';
import { Sheet, SheetContent, SheetDescription, SheetHeader, SheetTitle, SheetTrigger } from '@/Components/ui/sheet';
import { Textarea } from '@/Components/ui/textarea';
import { flashMessage } from '@/lib/utils';
import { useForm } from '@inertiajs/react';
import { IconChecklist } from '@tabler/icons-react';
import { toast } from 'sonner';

export default function Approve({ conditions, returnBook, action }) {
    const { data, setData, put, errors, processing } = useForm({
        condition: '',
        notes: '',
        _method: 'PUT',
    });

    const onHandleSubmit = (e) => {
        e.preventDefault();

        put(action, {
            preserveScroll: true,
            preserveState: true,
            onSuccess: (success) => {
                const flash = flashMessage(success);

                if (flash) toast[flash.type](flash.message);
            },
        });
    };

    return (
        <Sheet>
            <SheetTrigger asChild>
                <Button variant="green" size="sm">
                    <IconChecklist className="size-4 text-white" />
                </Button>
            </SheetTrigger>
            <SheetContent>
                <SheetHeader>
                    <SheetTitle>Konfirmasi Kondisi Buku</SheetTitle>
                    <SheetDescription>
                        Periksa kondisi buku sesuai dengan buku yang dikembalikan oleh member
                    </SheetDescription>
                </SheetHeader>

                <Card className="my-4 py-4">
                    <CardContent className="pb-0">
                        <div className="flex flex-col items-start gap-6">
                            <div className="h-20 w-20 flex-shrink-0 overflow-hidden rounded-lg bg-gray-200 lg:h-40 lg:w-40">
                                <img
                                    src={returnBook.book.cover}
                                    alt={returnBook.book.title}
                                    className="h-full w-full object-cover object-center"
                                />
                            </div>
                            <div className="space-y-1">
                                <dt className="text-xs font-medium text-muted-foreground">Judul Buku</dt>
                                <dd className="text-sm text-foreground">{returnBook.book.title}</dd>
                            </div>
                            <div className="space-y-1">
                                <dt className="text-xs font-medium text-muted-foreground">Nama Member</dt>
                                <dd className="text-sm text-foreground">{returnBook.user.name}</dd>
                            </div>
                            <div className="space-y-1">
                                <dt className="text-xs font-medium text-muted-foreground">Tanggal Pengembalian</dt>
                                <dd className="text-sm text-foreground">{returnBook.return_date.formatted}</dd>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <form className="mt-6 space-y-6" onSubmit={onHandleSubmit}>
                    <div className="grid w-full items-center gap-1.5">
                        <Label htmlFor="condition">Kondisi Buku</Label>
                        <Select defaultValue={data.condition} onValueChange={(value) => setData('condition', value)}>
                            <SelectTrigger>
                                <SelectValue placeholder="Pilih kondisi"></SelectValue>
                            </SelectTrigger>
                            <SelectContent>
                                {conditions.map((condition, index) => (
                                    <SelectItem key={index} value={condition.value}>
                                        {condition.label}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                        {errors.condition && <InputError message={errors.condition} />}
                    </div>
                    <div className="grid w-full items-center gap-1.5">
                        <Label htmlFor="notes">Catatan</Label>
                        <Textarea
                            name="notes"
                            placeholder="Isi catatan pengembalian jika diperlukan"
                            value={data.notes}
                            onChange={(e) => setData('notes', e.target.value)}
                        />
                        {errors.notes && <InputError message={errors.notes} />}
                    </div>
                    <div>
                        <Button variant="orange" type="submit" disabled={processing}>
                            Simpan
                        </Button>
                    </div>
                </form>
            </SheetContent>
        </Sheet>
    );
}
