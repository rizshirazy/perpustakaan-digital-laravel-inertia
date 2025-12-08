import GetFineStatusBadge from '@/Components/GetFineStatusBadge';
import HeaderTitle from '@/Components/HeaderTitle';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/Components/ui/card';
import { Pagination, PaginationContent, PaginationItem, PaginationLink } from '@/Components/ui/pagination';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table';
import AppLayout from '@/Layouts/AppLayout';
import { formatToRupiah } from '@/lib/utils';
import { Link } from '@inertiajs/react';
import { IconEye, IconMoneybag } from '@tabler/icons-react';

export default function Index(props) {
    const { data: fines, meta } = props.fines;

    return (
        <div className="flex w-full flex-col space-y-4 pb-32">
            <div className="flex flex-col items-start justify-between gap-y-5 lg:flex-row lg:items-center">
                <HeaderTitle
                    title={props.page_settings.title}
                    subtitle={props.page_settings.subtitle}
                    icon={IconMoneybag}
                />
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Tabel Denda</CardTitle>
                    <CardDescription>Menampilkan rincian semua denda</CardDescription>
                </CardHeader>
                <CardContent className="p-0 [&_td]:whitespace-nowrap [&_td]:px-6 [&_th]:px-6">
                    <Table className="w-full">
                        <TableHeader>
                            <TableRow>
                                <TableHead>#</TableHead>
                                <TableHead>Kode Peminjaman</TableHead>
                                <TableHead>Kode Pengembalian</TableHead>
                                <TableHead>Tanggal Peminjaman</TableHead>
                                <TableHead>Batas Pengembalian</TableHead>
                                <TableHead>Tanggal Pengembalian</TableHead>
                                <TableHead>Denda Keterlambatan</TableHead>
                                <TableHead>Denda Lainnya</TableHead>
                                <TableHead>Total Denda</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead>Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {fines.map((fine, index) => (
                                <TableRow key={fine.id}>
                                    <TableCell>{index + 1}</TableCell>
                                    <TableCell>{fine.return_book.loan.loan_code}</TableCell>
                                    <TableCell>{fine.return_book.return_code}</TableCell>
                                    <TableCell>{fine.return_book.loan.loan_date.formatted}</TableCell>
                                    <TableCell>{fine.return_book.loan.due_date.formatted}</TableCell>
                                    <TableCell>{fine.return_book.return_date.formatted}</TableCell>
                                    <TableCell>
                                        <div className="flex flex-col">
                                            {fine.return_book.days_late > 0 && (
                                                <span className="text-xs text-muted-foreground">
                                                    {fine.return_book.days_late} hari
                                                </span>
                                            )}
                                            <span className="text-red-500">{formatToRupiah(fine.late_fee)}</span>
                                        </div>
                                    </TableCell>
                                    <TableCell>
                                        <div className="flex flex-col">
                                            {fine.return_book.return_book_check && (
                                                <span className="text-xs text-muted-foreground">
                                                    {fine.return_book.return_book_check.condition.label}
                                                </span>
                                            )}
                                            <span className="text-red-500">{formatToRupiah(fine.other_fee)}</span>
                                        </div>
                                    </TableCell>
                                    <TableCell className="font-semibold text-red-500">
                                        {formatToRupiah(fine.total_fee)}
                                    </TableCell>
                                    <TableCell>
                                        <GetFineStatusBadge status={fine.payment_status} />
                                    </TableCell>
                                    <TableCell>
                                        <div className="flex items-center gap-x-1">
                                            <Button variant="blue" size="sm" asChild>
                                                <Link
                                                    href={route('front.return-books.show', [
                                                        fine.return_book.return_code,
                                                    ])}
                                                >
                                                    <IconEye className="size-4" />
                                                </Link>
                                            </Button>
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
                        dari {meta.total} denda
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
