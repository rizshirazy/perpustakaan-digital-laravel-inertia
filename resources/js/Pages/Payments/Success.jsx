import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import { Head, Link } from '@inertiajs/react';
import { IconCircleCheck } from '@tabler/icons-react';

export default function Success() {
    return (
        <>
            <Head title="Pemayaran Berhasil" />
            <div className="flex min-h-screen items-center justify-center">
                <div className="mx-auto max-w-sm">
                    <Card>
                        <CardHeader className="flex flex-row items-center gap-x-2">
                            <IconCircleCheck className="text-green-500" />
                            <div>
                                <CardTitle>Berhasil</CardTitle>
                                <CardDescription>Pembayaran telah berhasil diproses</CardDescription>
                            </div>
                        </CardHeader>
                        <CardContent className="flex flex-col gap-y-6">
                            <p className="items-start text-foreground">
                                Terima kasih telah menyelesaikan pembayaran denda. Kami dengan senang hati
                                mengkonfirmasi berhasil diproses. bahwa transaksi anda
                            </p>
                            <Button variant="orange" asChild>
                                <Link href={route('dashboard')}>Kembali</Link>
                            </Button>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </>
    );
}
