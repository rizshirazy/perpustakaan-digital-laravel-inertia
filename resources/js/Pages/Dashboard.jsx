import CardStat from '@/Components/CardStat';
import HeaderTitle from '@/Components/HeaderTitle';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table';
import AppLayout from '@/Layouts/AppLayout';
import { formatToRupiah } from '@/lib/utils';
import { Link } from '@inertiajs/react';
import {
    IconArrowUpRight,
    IconBooks,
    IconCreditCardPay,
    IconCreditCardRefund,
    IconDashboard,
    IconMoneybag,
    IconUsersGroup,
} from '@tabler/icons-react';

export default function Dashboard(props) {
    const auth = props.auth.user;

    const { loans, return_books, total_books, total_users, total_loans, total_returned_books, unpaid_fines } =
        props.page_data;

    return (
        <div className="flex w-full flex-col space-y-4 pb-32">
            <div className="flex flex-col items-start justify-between gap-y-4 lg:flex-row lg:items-center">
                <HeaderTitle
                    title={props.page_settings.title}
                    subtitle={props.page_settings.subtitle}
                    icon={IconDashboard}
                />
            </div>

            {auth.role.some((role) => ['admin', 'operator'].includes(role)) && (
                <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4 lg:gap-8">
                    <CardStat
                        data={{
                            title: 'Total Buku',
                            icon: IconBooks,
                            background: 'text-white bg-gradient-to-r from-blue-400 via-blue-500 to-blue-500',
                            iconClassName: 'text-white',
                        }}
                    >
                        <div className="text-2xl font-bold">{total_books}</div>
                    </CardStat>
                    <CardStat
                        data={{
                            title: 'Total Pengguna',
                            icon: IconUsersGroup,
                            background: 'text-white bg-gradient-to-r from-purple-400 via-purple-500 to-purple-500',
                            iconClassName: 'text-white',
                        }}
                    >
                        <div className="text-2xl font-bold">{total_users}</div>
                    </CardStat>
                    <CardStat
                        data={{
                            title: 'Total Peminjaman',
                            icon: IconCreditCardPay,
                            background: 'text-white bg-gradient-to-r from-rose-400 via-rose-500 to-rose-500',
                            iconClassName: 'text-white',
                        }}
                    >
                        <div className="text-2xl font-bold">{total_loans}</div>
                    </CardStat>
                    <CardStat
                        data={{
                            title: 'Total Pengembalian',
                            icon: IconCreditCardRefund,
                            background: 'text-white bg-gradient-to-r from-lime-400 via-lime-500 to-lime-500',
                            iconClassName: 'text-white',
                        }}
                    >
                        <div className="text-2xl font-bold">{total_returned_books}</div>
                    </CardStat>
                </div>
            )}

            {auth.role.some((role) => ['member'].includes(role)) && (
                <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3 lg:gap-8">
                    <CardStat
                        data={{
                            title: 'Total Peminjaman',
                            icon: IconCreditCardPay,
                            background: 'text-white bg-gradient-to-r from-blue-400 via-blue-500 to-blue-500',
                            iconClassName: 'text-white',
                        }}
                    >
                        <div className="text-2xl font-bold">{total_loans}</div>
                    </CardStat>
                    <CardStat
                        data={{
                            title: 'Total Pengembalian',
                            icon: IconCreditCardRefund,
                            background: 'text-white bg-gradient-to-r from-purple-400 via-purple-500 to-purple-500',
                            iconClassName: 'text-white',
                        }}
                    >
                        <div className="text-2xl font-bold">{total_returned_books}</div>
                    </CardStat>
                    <CardStat
                        data={{
                            title: 'Denda',
                            icon: IconMoneybag,
                            background: 'text-white bg-gradient-to-r from-rose-400 via-rose-500 to-rose-500',
                            iconClassName: 'text-white',
                        }}
                    >
                        <div className="text-2xl font-bold">{formatToRupiah(unpaid_fines)}</div>
                    </CardStat>
                </div>
            )}

            <div className="flex w-full flex-col justify-between gap-2 lg:flex-row">
                <Card className="w-full lg:w-1/2">
                    <CardHeader>
                        <div className="flex flex-col justify-between gap-y-4 lg:flex-row lg:items-center">
                            <div className="flex flex-col gap-y-2">
                                <CardTitle>Transaksi Peminjaman</CardTitle>
                                <CardDescription>Transaksi peminjaman terakhir</CardDescription>
                            </div>
                            <Button variant="outline" asChild>
                                {auth.role.some((role) => ['admin', 'operator'].includes(role)) ? (
                                    <Link href={route('admin.loans.index')}>
                                        Lihat Semua <IconArrowUpRight className="size-4" />
                                    </Link>
                                ) : (
                                    <Link href="#">
                                        Lihat Semua <IconArrowUpRight className="size-4" />
                                    </Link>
                                )}
                            </Button>
                        </div>
                    </CardHeader>
                    <CardContent className="p-0 [&_td]:whitespace-nowrap [&_td]:px-6 [&_th]:px-6">
                        <Table className="w-full">
                            <TableHeader>
                                <TableRow>
                                    <TableHead>#</TableHead>
                                    <TableHead>Kode Peminjaman</TableHead>
                                    <TableHead>Buku</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {loans.length > 0 ? (
                                    loans.map((loan, index) => (
                                        <TableRow key={index}>
                                            <TableCell>{index + 1}</TableCell>
                                            <TableCell>{loan.loan_code}</TableCell>
                                            <TableCell>{loan.book.title}</TableCell>
                                        </TableRow>
                                    ))
                                ) : (
                                    <TableRow>
                                        <TableCell colSpan={3} className="text-center">
                                            Tidak ada data
                                        </TableCell>
                                    </TableRow>
                                )}
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>

                <Card className="w-full lg:w-1/2">
                    <CardHeader>
                        <div className="flex flex-col justify-between gap-y-4 lg:flex-row lg:items-center">
                            <div className="flex flex-col gap-y-2">
                                <CardTitle>Transaksi Pengembalian</CardTitle>
                                <CardDescription>Transaksi pengembalian terakhir</CardDescription>
                            </div>
                            <Button variant="outline" asChild>
                                {auth.role.some((role) => ['admin', 'operator'].includes(role)) ? (
                                    <Link href={route('admin.return-books.index')}>
                                        Lihat Semua <IconArrowUpRight className="size-4" />
                                    </Link>
                                ) : (
                                    <Link href="#">
                                        Lihat Semua <IconArrowUpRight className="size-4" />
                                    </Link>
                                )}
                            </Button>
                        </div>
                    </CardHeader>
                    <CardContent className="p-0 [&_td]:whitespace-nowrap [&_td]:px-6 [&_th]:px-6">
                        <Table className="w-full">
                            <TableHeader>
                                <TableRow>
                                    <TableHead>#</TableHead>
                                    <TableHead>Kode Pengembalian</TableHead>
                                    <TableHead>Buku</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {return_books.length > 0 ? (
                                    return_books.map((return_book, index) => (
                                        <TableRow key={index}>
                                            <TableCell>{index + 1}</TableCell>
                                            <TableCell>{return_book.return_code}</TableCell>
                                            <TableCell>{return_book.book.title}</TableCell>
                                        </TableRow>
                                    ))
                                ) : (
                                    <TableRow>
                                        <TableCell colSpan={3} className="text-center">
                                            Tidak ada data
                                        </TableCell>
                                    </TableRow>
                                )}
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>
            </div>
        </div>
    );
}

Dashboard.layout = (page) => <AppLayout title={page.props.page_settings.title}>{page}</AppLayout>;
