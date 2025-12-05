import GetFineStatusBadge from '@/Components/GetFineStatusBadge';
import HeaderTitle from '@/Components/HeaderTitle';
import { Alert, AlertDescription } from '@/Components/ui/alert';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardFooter, CardHeader } from '@/Components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table';
import AppLayout from '@/Layouts/AppLayout';
import { FINEPAYMENTSTATUS, formatToRupiah } from '@/lib/utils';
import { Link } from '@inertiajs/react';
import { IconArrowLeft, IconCircleCheck, IconCreditCardRefund } from '@tabler/icons-react';

export default function Show(props) {
    const { return_book } = props;
    const { SUCCESS } = FINEPAYMENTSTATUS;

    const isPaymentSuccessful = return_book.fine?.payment_status !== SUCCESS;

    return (
        <div className="flex w-full flex-col space-y-4 pb-32">
            <div className="mb-8 flex flex-col items-start justify-between gap-y-4 lg:flex-row lg:items-center">
                <HeaderTitle
                    title={props.page_settings.title}
                    subtitle={props.page_settings.subtitle}
                    icon={IconCreditCardRefund}
                />

                <Button variant="outline" size="lg" asChild>
                    <Link href={route('front.return-books.index')}>
                        <IconArrowLeft size="4" /> Kembali
                    </Link>
                </Button>
            </div>

            <Card>
                <CardHeader className="flex flex-col gap-6 border-b border-muted text-sm lg:flex-row lg:items-center lg:justify-between lg:px-6">
                    <div>
                        <dt className="font-medium text-foreground">Kode Peminjaman</dt>
                        <dd className="mt-1 text-muted-foreground">{return_book.loan.loan_code}</dd>
                    </div>
                    <div>
                        <dt className="font-medium text-foreground">Tanggal Peminjaman</dt>
                        <dd className="mt-1 text-muted-foreground">{return_book.loan.loan_date.formatted}</dd>
                    </div>
                    <div>
                        <dt className="font-medium text-foreground">Kode Pengembalian</dt>
                        <dd className="mt-1 text-muted-foreground">{return_book.return_code}</dd>
                    </div>
                    <div>
                        <dt className="font-medium text-foreground">Status Pengembalian</dt>
                        <dd className="mt-1 text-muted-foreground">{return_book.status.label}</dd>
                    </div>
                </CardHeader>
                <CardContent className="divide-y divide-gray-200 py-6">
                    <div className="flex items-center lg:items-start">
                        <div className="h-20 w-20 flex-shrink-0 overflow-hidden rounded-lg bg-gray-200 lg:h-40 lg:w-40">
                            <img
                                src={return_book.loan.book.cover}
                                alt={return_book.loan.book.title}
                                className="h-full w-full object-cover object-center"
                            />
                        </div>
                        <div className="ml-6 flex-1 text-sm">
                            <h5 className="text-lg font-bold leading-relaxed">{return_book.loan.book.title}</h5>
                            <p className="hidden text-muted-foreground lg:mt-2 lg:block">
                                {return_book.loan.book.synopsis}
                            </p>
                        </div>
                    </div>
                </CardContent>
                <CardFooter className="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                    <div className="flex items-center">
                        <IconCircleCheck className="size-5 text-green-500" />
                        <p className="ml-2 text-sm font-medium text-muted-foreground">
                            Dikembalikan pada{' '}
                            <time dateTime={return_book.return_date.formatted}>
                                {return_book.return_date.formatted}
                            </time>
                        </p>
                    </div>

                    <div className="flex pt-6 text-sm font-medium lg:items-center lg:border-none lg:pt-0">
                        <div className="flex flex-1 justify-center">
                            <Button variant="link" asChild>
                                <Link href={route('front.books.show', [return_book.loan.book.slug])}>Lihat Buku</Link>
                            </Button>
                        </div>
                    </div>
                </CardFooter>
            </Card>

            {return_book.fine && (
                <div className="space-y-4">
                    <h2 className="font-semibold leading-relaxed text-foreground">Informasi Denda</h2>

                    {isPaymentSuccessful && (
                        <Alert variant="destructive">
                            <AlertDescription>
                                Sehubungan dengan proses pemeriksaan peminjaman, ditemukan adanya denda yang harus
                                diselesaikan terkait pengembalian buku. Kami mohon kesediaan Anda untuk segera melakukan
                                pembayaran denda tersebut.
                            </AlertDescription>
                        </Alert>
                    )}

                    <Card>
                        <CardContent className="space-y-6 p-6">
                            <div>
                                <div className="rounded-lg px-4 py-6">
                                    <Table>
                                        <TableHeader>
                                            <TableRow>
                                                <TableHead>Tanggal Peminjaman</TableHead>
                                                <TableHead>Tanggal Pengembalian</TableHead>
                                                <TableHead>Denda Keterlambatan</TableHead>
                                                <TableHead>Denda Lainnya</TableHead>
                                                <TableHead>Total Denda</TableHead>
                                                <TableHead>Status Pembayaran</TableHead>
                                                {isPaymentSuccessful && <TableHead>Aksi</TableHead>}
                                            </TableRow>
                                        </TableHeader>
                                        <TableBody>
                                            <TableRow>
                                                <TableCell className="align-top">
                                                    {return_book.loan.loan_date.formatted}
                                                </TableCell>
                                                <TableCell className="align-top">
                                                    {return_book.return_date.formatted}
                                                </TableCell>
                                                <TableCell className="align-top">
                                                    <div className="flex flex-col">
                                                        <span>{formatToRupiah(return_book.fine.late_fee)}</span>
                                                        {return_book.days_late > 0 && (
                                                            <span className="mt-1 text-xs text-red-500">
                                                                Terlambat {return_book.days_late} hari
                                                            </span>
                                                        )}
                                                    </div>
                                                </TableCell>
                                                <TableCell className="align-top">
                                                    <div className="flex flex-col">
                                                        <span>{formatToRupiah(return_book.fine.other_fee)}</span>
                                                        {return_book.return_book_check && (
                                                            <span className="mt-1 text-xs text-red-500">
                                                                Kondisi: {return_book.return_book_check.condition.label}
                                                            </span>
                                                        )}
                                                    </div>
                                                </TableCell>
                                                <TableCell className="align-top font-semibold">
                                                    {formatToRupiah(return_book.fine.total_fee)}
                                                </TableCell>
                                                <TableCell className="align-top">
                                                    <GetFineStatusBadge status={return_book.fine.payment_status} />
                                                </TableCell>
                                                {isPaymentSuccessful && (
                                                    <TableCell className="align-top">
                                                        <Button variant="blue" size="sm">
                                                            Bayar
                                                        </Button>
                                                    </TableCell>
                                                )}
                                            </TableRow>
                                        </TableBody>
                                    </Table>

                                    <p className="mt-8 text-sm">
                                        <span className="font-medium">Catatan Petugas: </span>
                                        {return_book.return_book_check.notes}
                                    </p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            )}
        </div>
    );
}

Show.layout = (page) => <AppLayout title={page.props.page_settings.title}>{page}</AppLayout>;
