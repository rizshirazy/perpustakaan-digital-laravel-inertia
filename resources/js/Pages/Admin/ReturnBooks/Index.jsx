import HeaderTitle from '@/Components/HeaderTitle';
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from '@/Components/ui/alert-dialog';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardFooter, CardHeader } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Pagination, PaginationContent, PaginationItem, PaginationLink } from '@/Components/ui/pagination';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table';
import { useFilter } from '@/hooks/UseFilter';
import AppLayout from '@/Layouts/AppLayout';
import { flashMessage, formatToRupiah } from '@/lib/utils';
import { router } from '@inertiajs/react';
import { IconArrowsUpDown, IconCreditCardRefund, IconRefresh, IconTrash } from '@tabler/icons-react';
import { useState } from 'react';
import { toast } from 'sonner';

export default function Index(props) {
    const { data: return_books, meta } = props.return_books;
    const [params, setParams] = useState(props.state);

    useFilter({
        route: route('admin.return-books.index'),
        values: params,
        only: ['return_books'],
    });

    const onSortable = (field) => {
        setParams({ ...params, field: field, direction: params.direction === 'asc' ? 'desc' : 'asc' });
    };

    const onHandleDelete = (id) => {
        router.delete(route('admin.return_books.destroy', id), {
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
                    icon={IconCreditCardRefund}
                />

                {/* <Button variant="orange" size="lg" asChild>
                    <Link href={route('admin.return-books.create')}>
                        <IconPlus className="size-4" /> Tambah
                    </Link>
                </Button> */}
            </div>

            <Card>
                <CardHeader>
                    <div className="flex w-full flex-col gap-4 lg:flex-row lg:items-center">
                        <Input
                            className="w-full sm:w-1/4"
                            placeholder="Search..."
                            value={params?.search}
                            onChange={(e) => setParams((prev) => ({ ...prev, search: e.target.value, page: 1 }))}
                        />
                        <Select value={params?.load} onValueChange={(e) => setParams({ ...params, load: e })}>
                            <SelectTrigger className="w-full sm:w-24">
                                <SelectValue placeholder="Load" />
                                <SelectContent>
                                    {[10, 25, 50, 100].map((number, index) => (
                                        <SelectItem key={index} value={number}>
                                            {number}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </SelectTrigger>
                        </Select>
                        <Button variant="red" size="xl" onClick={() => setParams(props.state)}>
                            <IconRefresh size={4} /> Reset
                        </Button>
                    </div>
                </CardHeader>
                <CardContent className="p-0 [&-td]:whitespace-nowrap [&_td]:px-6 [&_th]:px-6">
                    <Table className="w-full text-sm">
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-16">
                                    <Button
                                        variant="ghost"
                                        className="group inline-flex"
                                        onClick={() => onSortable('id')}
                                    >
                                        #
                                        <span className="ml-2 flex-none rounded text-muted-foreground">
                                            <IconArrowsUpDown size={4} />
                                        </span>
                                    </Button>
                                </TableHead>
                                <TableHead className="w-44">
                                    <Button
                                        variant="ghost"
                                        className="group inline-flex"
                                        onClick={() => onSortable('return_code')}
                                    >
                                        Kode Pengembalian
                                        <span className="ml-2 flex-none rounded text-muted-foreground">
                                            <IconArrowsUpDown size={4} />
                                        </span>
                                    </Button>
                                </TableHead>
                                <TableHead className="w-48">
                                    <Button
                                        variant="ghost"
                                        className="group inline-flex"
                                        onClick={() => onSortable('loan_id')}
                                    >
                                        Kode Peminjaman
                                        <span className="ml-2 flex-none rounded text-muted-foreground">
                                            <IconArrowsUpDown size={4} />
                                        </span>
                                    </Button>
                                </TableHead>
                                <TableHead className="w-48">
                                    <Button
                                        variant="ghost"
                                        className="group inline-flex"
                                        onClick={() => onSortable('user_id')}
                                    >
                                        Nama Pengguna
                                        <span className="ml-2 flex-none rounded text-muted-foreground">
                                            <IconArrowsUpDown size={4} />
                                        </span>
                                    </Button>
                                </TableHead>
                                <TableHead className="w-64">
                                    <Button
                                        variant="ghost"
                                        className="group inline-flex"
                                        onClick={() => onSortable('book_id')}
                                    >
                                        Judul Buku
                                        <span className="ml-2 flex-none rounded text-muted-foreground">
                                            <IconArrowsUpDown size={4} />
                                        </span>
                                    </Button>
                                </TableHead>
                                <TableHead className="min-w-42">
                                    <Button
                                        variant="ghost"
                                        className="group inline-flex"
                                        onClick={() => onSortable('due_date')}
                                    >
                                        Batas Pengembalian
                                        <span className="ml-2 flex-none rounded text-muted-foreground">
                                            <IconArrowsUpDown size={4} />
                                        </span>
                                    </Button>
                                </TableHead>
                                <TableHead className="min-w-42">
                                    <Button
                                        variant="ghost"
                                        className="group inline-flex"
                                        onClick={() => onSortable('return_date')}
                                    >
                                        Tanggal Pengembalian
                                        <span className="ml-2 flex-none rounded text-muted-foreground">
                                            <IconArrowsUpDown size={4} />
                                        </span>
                                    </Button>
                                </TableHead>
                                <TableHead className="min-w-42">
                                    <Button
                                        variant="ghost"
                                        className="group inline-flex"
                                        onClick={() => onSortable('status')}
                                    >
                                        Status
                                        <span className="ml-2 flex-none rounded text-muted-foreground">
                                            <IconArrowsUpDown size={4} />
                                        </span>
                                    </Button>
                                </TableHead>
                                <TableHead>Kondisi</TableHead>
                                <TableHead>Denda</TableHead>
                                <TableHead className="w-28">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {return_books.map((return_book, index) => (
                                <TableRow key={index}>
                                    <TableCell>{index + 1 + (meta.current_page - 1) * meta.per_page}</TableCell>
                                    <TableCell>{return_book.return_code}</TableCell>
                                    <TableCell>{return_book.loan.loan_code}</TableCell>
                                    <TableCell className="whitespace-normal break-words">
                                        {return_book.user.name}
                                    </TableCell>
                                    <TableCell className="whitespace-normal break-words">
                                        {return_book.book.title}
                                    </TableCell>
                                    <TableCell>{return_book.loan.due_date.formatted}</TableCell>
                                    <TableCell>{return_book.return_date.formatted}</TableCell>
                                    <TableCell>{return_book.status.label}</TableCell>
                                    <TableCell>{return_book.return_book_check.condition.label}</TableCell>
                                    <TableCell className="text-red-500">{formatToRupiah(return_book.fine)}</TableCell>
                                    <TableCell>
                                        <div className="flex items-center gap-x-1">
                                            <AlertDialog>
                                                <AlertDialogTrigger asChild>
                                                    <Button variant="red" size="sm">
                                                        <IconTrash size="4" />
                                                    </Button>
                                                </AlertDialogTrigger>
                                                <AlertDialogContent>
                                                    <AlertDialogHeader>
                                                        <AlertDialogTitle>
                                                            Apakah anda yakin menghapus transaksi pengembalian{' '}
                                                            {return_book.return_code}?
                                                        </AlertDialogTitle>
                                                        <AlertDialogDescription>
                                                            Tindakan ini tidak dapat dibatalkan dan akan menghapus
                                                            transaksi pengembalian beserta seluruh data yang berhubungan
                                                            dengan transaksi pengembalian ini.
                                                        </AlertDialogDescription>
                                                    </AlertDialogHeader>
                                                    <AlertDialogFooter>
                                                        <AlertDialogCancel>Batal</AlertDialogCancel>
                                                        <AlertDialogAction onClick={() => onHandleDelete(return_book)}>
                                                            Hapus
                                                        </AlertDialogAction>
                                                    </AlertDialogFooter>
                                                </AlertDialogContent>
                                            </AlertDialog>
                                        </div>
                                    </TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                </CardContent>
                <CardFooter className="flex w-full flex-col items-center justify-between border-t py-2 lg:flex-row">
                    <p className="my-2 text-sm text-muted-foreground">
                        Menampilkan{' '}
                        <span className="font-medium text-orange-500">
                            {meta.from && meta.to ? meta.to - meta.from + 1 : 0}{' '}
                        </span>{' '}
                        dari {meta.total} pengembalian
                    </p>
                    <div className="overflow-x-auto">
                        {meta.has_pages && (
                            <Pagination>
                                <PaginationContent className="flex flex-wrap justify-center lg:justify-end">
                                    {meta.links.map((link, index) => (
                                        <PaginationItem key={index} className="mx-[0.5] mb-1 lg:mb-0">
                                            <PaginationLink href={link.url} isActive={link.active}>
                                                {link.label}
                                            </PaginationLink>
                                        </PaginationItem>
                                    ))}
                                </PaginationContent>
                            </Pagination>
                        )}
                    </div>
                </CardFooter>
            </Card>
        </div>
    );
}

Index.layout = (page) => <AppLayout title={page.props.page_settings.title}>{page}</AppLayout>;
