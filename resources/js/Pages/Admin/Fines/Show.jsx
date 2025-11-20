import GetFineStatusBadge from '@/Components/GetFineStatusBadge';
import HeaderTitle from '@/Components/HeaderTitle';
import { Badge } from '@/Components/ui/badge';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import AppLayout from '@/Layouts/AppLayout';
import { Link } from '@inertiajs/react';
import { IconArrowLeft, IconMoneybagMove } from '@tabler/icons-react';

export default function Show(props) {
    const { loan, user, book, fine, return_code, status, return_date, return_book_check, days_late } = props.page_data;

    const formatCurrency = (value) => {
        if (value === null || value === undefined) return '-';
        return `Rp ${Number(value).toLocaleString('id-ID')}`;
    };

    const FieldRow = ({ label, value }) => (
        <div className="flex items-start justify-between gap-4 border-b border-muted-foreground/10 px-6 py-3 last:border-0">
            <span className="text-sm font-medium text-muted-foreground">{label}</span>
            <span className="max-w-[65%] text-right text-sm text-foreground">{value ?? '-'}</span>
        </div>
    );

    return (
        <div className="flex w-full flex-col space-y-4 pb-32 lg:space-y-8">
            <div className="flex flex-col items-start justify-between gap-y-4 lg:flex-row lg:items-center">
                <HeaderTitle
                    title={props.page_settings.title}
                    subtitle="Ringkasan denda dan detail pengembalian untuk transaksi ini."
                    icon={IconMoneybagMove}
                />

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
                    <CardContent className="divide-y divide-muted-foreground/10 p-0">
                        <FieldRow label="Nama" value={user.name} />
                        <FieldRow label="Username" value={user.username} />
                        <FieldRow label="Email" value={user.email} />
                        <FieldRow label="Nomor Telepon" value={user.phone} />
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Data Buku</CardTitle>
                    </CardHeader>
                    <CardContent className="divide-y divide-muted-foreground/10 p-0">
                        <FieldRow label="Kode Buku" value={book.book_code} />
                        <FieldRow label="Judul Buku" value={book.title} />
                        <FieldRow label="Penulis" value={book.author} />
                        <FieldRow label="Penerbit" value={book.publisher?.name} />
                    </CardContent>
                </Card>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Detail Pengembalian & Denda</CardTitle>
                </CardHeader>
                <CardContent className="divide-y divide-muted-foreground/10 p-0">
                    <div className="grid grid-cols-1 gap-0 lg:grid-cols-2">
                        <div className="divide-y divide-muted-foreground/10">
                            <FieldRow label="Kode Peminjaman" value={loan.loan_code} />
                            <FieldRow label="Tanggal Peminjaman" value={loan.loan_date.formatted} />
                            <FieldRow label="Batas Pengembalian" value={loan.due_date.formatted} />
                            <FieldRow label="Kode Pengembalian" value={return_code} />
                            <FieldRow label="Tanggal Pengembalian" value={return_date?.formatted} />
                            <FieldRow label="Keterlambatan" value={days_late ? `${days_late} hari` : '-'} />
                        </div>
                        <div className="divide-y divide-muted-foreground/10">
                            <FieldRow
                                label="Status Pengembalian"
                                value={
                                    status?.label ? (
                                        <Badge variant="outline" className="text-xs">
                                            {status.label}
                                        </Badge>
                                    ) : (
                                        '-'
                                    )
                                }
                            />
                            <FieldRow label="Kondisi Buku" value={return_book_check?.condition?.label} />
                            <FieldRow label="Denda Keterlambatan" value={formatCurrency(fine?.late_fee)} />
                            <FieldRow label="Denda Lain" value={formatCurrency(fine?.other_fee)} />
                            <FieldRow label="Total Denda" value={formatCurrency(fine?.total_fee)} />
                            <FieldRow
                                label="Status Pembayaran"
                                value={fine ? <GetFineStatusBadge status={fine.payment_status} /> : '-'}
                            />
                        </div>
                    </div>
                    {return_book_check?.notes && (
                        <div className="px-6 py-4">
                            <p className="text-sm font-medium text-muted-foreground">Catatan Petugas</p>
                            <p className="mt-1 text-sm text-foreground">{return_book_check.notes}</p>
                        </div>
                    )}
                </CardContent>
            </Card>
        </div>
    );
}

Show.layout = (page) => <AppLayout title={page.props.page_settings.title}>{page}</AppLayout>;
