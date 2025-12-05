import { Button } from '@/Components/ui/button';
import AppLayout from '@/Layouts/AppLayout';
import { flashMessage } from '@/lib/utils';
import { router } from '@inertiajs/react';
import { toast } from 'sonner';

export default function Show(props) {
    const { book } = props;
    return (
        <div className="flex w-full flex-col space-y-12 pb-32">
            <div className="lg:grid lg:grid-cols-12 lg:grid-rows-1 lg:gap-x-8 lg:gap-y-10">
                <div className="lg:col-span-4 lg:row-end-1">
                    <div className="max-w-sm overflow-hidden rounded-lg bg-gray-100">
                        <img src={book.cover} alt={book.title} />
                    </div>
                </div>

                <div className="mt-10 lg:col-span-8 lg:row-span-2 lg:row-end-2 lg:mt-0 lg:max-w-none">
                    <div className="flex flex-col-reverse">
                        <div className="mt-4">
                            <h2 className="text-xl font-bold tracking-tighter text-foreground">{book.title}</h2>
                            <span className="mt-4 text-xs text-muted-foreground">
                                Ditambahkan pada <time dateTime={book.created_at}>{book.created_at}</time>
                            </span>
                        </div>
                    </div>

                    <p className="mt-6 text-sm leading-relaxed text-muted-foreground">{book.synopsis}</p>

                    <div className="mt-10 flex">
                        {book.stock.available > 0 ? (
                            <Button
                                size="lg"
                                onClick={() =>
                                    router.post(
                                        route('front.loans.store', [book.slug]),
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
                                Pinjam Sekarang
                            </Button>
                        ) : (
                            <Button size="lg" disabled>
                                Buku Tidak Tersedia
                            </Button>
                        )}
                    </div>

                    <div className="mt-10 flex flex-col justify-start gap-10 border-t border-gray-200 pt-10 lg:flex-row">
                        <div>
                            <h3 className="text-sm font-medium text-foreground">Tahun Publikasi</h3>
                            <p className="mt-4 text-sm text-muted-foreground">{book.publication_year}</p>
                        </div>
                        <div>
                            <h3 className="text-sm font-medium text-foreground">ISBN</h3>
                            <p className="mt-4 text-sm text-muted-foreground">{book.isbn}</p>
                        </div>
                        <div>
                            <h3 className="text-sm font-medium text-foreground">Jumlah Halaman</h3>
                            <p className="mt-4 text-sm text-muted-foreground">{book.number_of_pages}</p>
                        </div>
                        <div>
                            <h3 className="text-sm font-medium text-foreground">Bahasa</h3>
                            <p className="mt-4 text-sm text-muted-foreground">{book.language.label}</p>
                        </div>
                    </div>

                    <div className="flex flex-col justify-start gap-10 border-gray-200 pt-10 lg:mt-10 lg:flex-row lg:border-t">
                        <div>
                            <h3 className="text-sm font-medium text-foreground">Penulis</h3>
                            <p className="mt-4 text-sm text-muted-foreground">{book.author}</p>
                        </div>
                        <div>
                            <h3 className="text-sm font-medium text-foreground">Kategori</h3>
                            <p className="mt-4 text-sm text-muted-foreground">{book.category.name}</p>
                        </div>
                        <div>
                            <h3 className="text-sm font-medium text-foreground">Penerbit</h3>
                            <p className="mt-4 text-sm text-muted-foreground">{book.publisher.name}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}

Show.layout = (page) => <AppLayout title={page.props.page_settings.title}>{page}</AppLayout>;
