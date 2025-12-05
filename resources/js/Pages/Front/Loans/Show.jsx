import HeaderTitle from '@/Components/HeaderTitle';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardFooter, CardHeader } from '@/Components/ui/card';
import AppLayout from '@/Layouts/AppLayout';
import { flashMessage } from '@/lib/utils';
import { Link, router } from '@inertiajs/react';
import { IconArrowLeft, IconCalendar, IconCreditCardPay } from '@tabler/icons-react';
import { toast } from 'sonner';

export default function Show(props) {
    return (
        <div className="flex w-full flex-col pb-32">
            <div className="mb-8 flex flex-col items-start justify-between gap-y-4 lg:flex-row lg:items-center">
                <HeaderTitle
                    title={props.page_settings.title}
                    subtitle={props.page_settings.subtitle}
                    icon={IconCreditCardPay}
                />

                <Button variant="outline" size="lg" asChild>
                    <Link href={route('front.loans.index')}>
                        <IconArrowLeft size="4" /> Kembali
                    </Link>
                </Button>
            </div>

            <Card>
                <CardHeader className="flex flex-col gap-6 border-b border-muted text-sm lg:flex-row lg:items-center lg:justify-between lg:px-6">
                    <div>
                        <dt className="font-medium text-foreground">Kode Peminjaman</dt>
                        <dd className="mt-1 text-muted-foreground">{props.loan.loan_code}</dd>
                    </div>
                    <div>
                        <dt className="font-medium text-foreground">Tanggal Peminjaman</dt>
                        <dd className="mt-1 text-muted-foreground">{props.loan.loan_date.formatted}</dd>
                    </div>
                </CardHeader>
                <CardContent className="divide-y divide-gray-200 py-6">
                    <div className="flex items-center lg:items-start">
                        <div className="h-20 w-20 flex-shrink-0 overflow-hidden rounded-lg bg-gray-200 lg:h-40 lg:w-40">
                            <img
                                src={props.loan.book.cover}
                                alt={props.loan.book.title}
                                className="h-full w-full object-cover object-center"
                            />
                        </div>
                        <div className="ml-6 flex-1 text-sm">
                            <h5 className="text-lg font-bold leading-relaxed">{props.loan.book.title}</h5>
                            <p className="hidden text-muted-foreground lg:mt-2 lg:block">{props.loan.book.synopsis}</p>
                        </div>
                    </div>
                </CardContent>
                <CardFooter className="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                    <div className="flex items-center">
                        <IconCalendar className="size-5 text-green-500" />
                        <p className="ml-2 text-sm font-medium text-muted-foreground">
                            Kembalikan sebelum{' '}
                            <time dateTime={props.loan.due_date.formatted}>{props.loan.due_date.formatted}</time>
                        </p>
                    </div>

                    <div className="flex pt-6 text-sm font-medium lg:items-center lg:border-none lg:pt-0">
                        <div className="flex flex-1 justify-center">
                            <Button variant="link" asChild>
                                <Link href={route('front.books.show', [props.loan.book.slug])}>Lihat Buku</Link>
                            </Button>
                            {!props.loan.has_return_book && (
                                <Button
                                    variant="orange"
                                    onClick={() =>
                                        router.post(
                                            route('front.return-books.store', [
                                                props.loan.book.slug,
                                                props.loan.loan_code,
                                            ]),
                                            {},
                                            {
                                                preserveScroll: true,
                                                preserveState: true,
                                                onSuccess: (success) => {
                                                    const flash = flashMessage(success);

                                                    if (flash) toast[flash.type](flash.message);
                                                },
                                            },
                                        )
                                    }
                                >
                                    Kembalikan
                                </Button>
                            )}
                        </div>
                    </div>
                </CardFooter>
            </Card>
        </div>
    );
}

Show.layout = (page) => <AppLayout title={page.props.page_settings.title}>{page}</AppLayout>;
