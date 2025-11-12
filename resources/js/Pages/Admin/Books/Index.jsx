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
import { flashMessage } from '@/lib/utils';
import { Link, router } from '@inertiajs/react';
import { IconArrowsUpDown, IconBooks, IconPencil, IconPlus, IconRefresh, IconTrash } from '@tabler/icons-react';
import { useState } from 'react';
import { toast } from 'sonner';

export default function Index(props) {
    const { data: books, meta } = props.books;
    const [params, setParams] = useState(props.state);

    useFilter({
        route: route('admin.books.index'),
        values: params,
        only: ['books'],
    });

    const onSortable = (field) => {
        setParams({ ...params, field: field, direction: params.direction === 'asc' ? 'desc' : 'asc' });
    };

    const onHandleDelete = (id) => {
        router.delete(route('admin.books.destroy', id), {
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
                    icon={IconBooks}
                />

                <Button variant="orange" size="lg" asChild>
                    <Link href={route('admin.books.create')}>
                        <IconPlus className="size-4" /> Tambah
                    </Link>
                </Button>
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
                        <Select value={params?.load} onValueChange={(e) => setParams({ ...params, load: e, page: 1 })}>
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
                    <Table className="w-full">
                        <TableHeader>
                            <TableRow>
                                <TableHead>#</TableHead>
                                <TableHead>
                                    <Button
                                        variant="ghost"
                                        className="group inline-flex"
                                        onClick={() => onSortable('book_code')}
                                    >
                                        Kode Buku
                                        <span className="ml-2 flex-none rounded text-muted-foreground">
                                            <IconArrowsUpDown size={4} />
                                        </span>
                                    </Button>
                                </TableHead>
                                <TableHead>
                                    <Button
                                        variant="ghost"
                                        className="group inline-flex"
                                        onClick={() => onSortable('title')}
                                    >
                                        Judul
                                        <span className="ml-2 flex-none rounded text-muted-foreground">
                                            <IconArrowsUpDown size={4} />
                                        </span>
                                    </Button>
                                </TableHead>
                                <TableHead>
                                    <Button
                                        variant="ghost"
                                        className="group inline-flex"
                                        onClick={() => onSortable('author')}
                                    >
                                        Penulis
                                        <span className="ml-2 flex-none rounded text-muted-foreground">
                                            <IconArrowsUpDown size={4} />
                                        </span>
                                    </Button>
                                </TableHead>
                                <TableHead>Stock</TableHead>
                                <TableHead>
                                    <Button
                                        variant="ghost"
                                        className="group inline-flex"
                                        onClick={() => onSortable('publication_year')}
                                    >
                                        Tahun Terbit
                                        <span className="ml-2 flex-none rounded text-muted-foreground">
                                            <IconArrowsUpDown size={4} />
                                        </span>
                                    </Button>
                                </TableHead>
                                <TableHead>
                                    <Button
                                        variant="ghost"
                                        className="group inline-flex"
                                        onClick={() => onSortable('isbn')}
                                    >
                                        ISBN
                                        <span className="ml-2 flex-none rounded text-muted-foreground">
                                            <IconArrowsUpDown size={4} />
                                        </span>
                                    </Button>
                                </TableHead>
                                <TableHead>
                                    <Button
                                        variant="ghost"
                                        className="group inline-flex"
                                        onClick={() => onSortable('language')}
                                    >
                                        Bahasa
                                        <span className="ml-2 flex-none rounded text-muted-foreground">
                                            <IconArrowsUpDown size={4} />
                                        </span>
                                    </Button>
                                </TableHead>
                                <TableHead className="min-w-36">Dibuat Pada</TableHead>
                                <TableHead>Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {books.map((book, index) => (
                                <TableRow key={index}>
                                    <TableCell>{index + 1 + (meta.current_page - 1) * meta.per_page}</TableCell>
                                    <TableCell>{book.book_code}</TableCell>
                                    <TableCell>{book.title}</TableCell>
                                    <TableCell>{book.author}</TableCell>
                                    <TableCell>{book.stock.total}</TableCell>
                                    <TableCell>{book.publication_year}</TableCell>
                                    <TableCell>{book.isbn}</TableCell>
                                    <TableCell>{book.language.label}</TableCell>
                                    <TableCell>{book.created_at}</TableCell>
                                    <TableCell>
                                        <div className="flex items-center gap-x-1">
                                            <Button variant="blue" size="sm" asChild>
                                                <Link href={route('admin.books.edit', [book])}>
                                                    <IconPencil size="4" />
                                                </Link>
                                            </Button>
                                            <AlertDialog>
                                                <AlertDialogTrigger asChild>
                                                    <Button variant="red" size="sm">
                                                        <IconTrash size="4" />
                                                    </Button>
                                                </AlertDialogTrigger>
                                                <AlertDialogContent>
                                                    <AlertDialogHeader>
                                                        <AlertDialogTitle>
                                                            Apakah anda yakin menghapus buku {book.title}?
                                                        </AlertDialogTitle>
                                                        <AlertDialogDescription>
                                                            Tindakan ini tidak dapat dibatalkan dan akan menghapus buku
                                                            beserta seluruh data yang berhubungan dengan buku ini.
                                                        </AlertDialogDescription>
                                                    </AlertDialogHeader>
                                                    <AlertDialogFooter>
                                                        <AlertDialogCancel>Batal</AlertDialogCancel>
                                                        <AlertDialogAction onClick={() => onHandleDelete(book)}>
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
                        dari {meta.total} buku
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
