import HeaderTitle from '@/Components/HeaderTitle';
import InputError from '@/Components/InputError';
import { Button } from '@/Components/ui/button';
import { Card, CardContent } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';
import { Textarea } from '@/Components/ui/textarea';
import AppLayout from '@/Layouts/AppLayout';
import { flashMessage } from '@/lib/utils';
import { Link, useForm } from '@inertiajs/react';
import { IconArrowLeft, IconBooks } from '@tabler/icons-react';
import { toast } from 'sonner';

export default function Create(props) {
    const {
        title,
        author,
        publication_year,
        isbn,
        language,
        synopsis,
        number_of_pages,
        price,
        stock,
        category,
        publisher,
    } = props.page_data.data;

    const { data, setData, reset, post, processing, errors } = useForm({
        title: title || '',
        author: author || '',
        publication_year: publication_year || '',
        isbn: isbn || '',
        language: language?.value || '',
        synopsis: synopsis || '',
        number_of_pages: number_of_pages || 0,
        price: price || 0,
        total: stock?.total || 0,
        category_id: category?.id || '',
        publisher_id: publisher?.id || '',
        cover: null,
        _method: props.page_settings.method,
    });

    const onHandleChange = (event) => {
        setData(event.target.name, event.target.type === 'file' ? event.target.files[0] : event.target.value);
    };

    const onHandleSubmit = (e) => {
        e.preventDefault();

        post(props.page_settings.action, {
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

                <Button variant="outline" size="lg" asChild>
                    <Link href={route('admin.books.index')}>
                        <IconArrowLeft size="4" /> Kembali
                    </Link>
                </Button>
            </div>

            <Card>
                <CardContent className="p-6">
                    <form onSubmit={onHandleSubmit} className="space-y-6">
                        <div className="grid w-full items-center gap-1.5">
                            <Label htmlFor="title">Judul</Label>
                            <Input
                                id="title"
                                name="title"
                                type="text"
                                placeholder="Masukkan judul buku"
                                value={data.title}
                                onChange={onHandleChange}
                            />
                            {errors.title && <InputError message={errors.title} />}
                        </div>

                        <div className="grid grid-cols-1 gap-6 lg:grid-cols-2">
                            <div className="grid w-full items-center gap-1.5">
                                <Label htmlFor="author">Penulis</Label>
                                <Input
                                    id="author"
                                    name="author"
                                    type="text"
                                    placeholder="Masukkan penulis buku"
                                    value={data.author}
                                    onChange={onHandleChange}
                                />
                                {errors.author && <InputError message={errors.author} />}
                            </div>

                            <div className="grid w-full items-center gap-1.5">
                                <Label htmlFor="publication_year">Tahun Terbit</Label>
                                <Select
                                    value={data.publication_year}
                                    onValueChange={(value) => setData('publication_year', value)}
                                >
                                    <SelectTrigger className="w-full">
                                        <SelectValue placeholder="Pilih tahun terbit" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {props.page_data.publicationYears.map((publication_year, index) => (
                                            <SelectItem key={index} value={String(publication_year)}>
                                                {publication_year}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                                {errors.publication_year && <InputError message={errors.publication_year} />}
                            </div>
                        </div>

                        <div className="grid grid-cols-1 gap-6 lg:grid-cols-2">
                            <div className="grid w-full items-center gap-1.5">
                                <Label htmlFor="isbn">
                                    ISBN{' '}
                                    <span className="text-xs text-muted-foreground">
                                        (International Standard Book Number)
                                    </span>
                                </Label>
                                <Input
                                    id="isbn"
                                    name="isbn"
                                    type="text"
                                    placeholder="Masukkan ISBN"
                                    value={data.isbn}
                                    onChange={onHandleChange}
                                />
                                {errors.isbn && <InputError message={errors.isbn} />}
                            </div>

                            <div className="grid w-full items-center gap-1.5">
                                <Label htmlFor="language">Bahasa</Label>
                                <Select value={data.language} onValueChange={(value) => setData('language', value)}>
                                    <SelectTrigger className="w-full">
                                        <SelectValue placeholder="Pilih Bahasa" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {props.page_data.languages?.map((language, index) => (
                                            <SelectItem key={index} value={language.value}>
                                                {language.label}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>

                                {errors.language && <InputError message={errors.language} />}
                            </div>
                        </div>

                        <div className="grid grid-cols-1 gap-6 lg:grid-cols-3">
                            <div className="grid w-full items-center gap-1.5">
                                <Label htmlFor="isbn">Jumlah Halaman</Label>
                                <Input
                                    id="number_of_pages"
                                    name="number_of_pages"
                                    type="number"
                                    value={data.number_of_pages}
                                    onChange={onHandleChange}
                                />
                                {errors.number_of_pages && <InputError message={errors.number_of_pages} />}
                            </div>

                            <div className="grid w-full items-center gap-1.5">
                                <Label htmlFor="price">Harga</Label>
                                <Input
                                    id="price"
                                    name="price"
                                    type="number"
                                    value={data.price}
                                    onChange={onHandleChange}
                                />
                                {errors.price && <InputError message={errors.price} />}
                            </div>

                            <div className="grid w-full items-center gap-1.5">
                                <Label htmlFor="total">Stok</Label>
                                <Input
                                    id="total"
                                    name="total"
                                    type="number"
                                    value={data.total}
                                    onChange={onHandleChange}
                                />
                                {errors.total && <InputError message={errors.total} />}
                            </div>
                        </div>

                        <div className="grid w-full items-center gap-1.5">
                            <Label htmlFor="synopsis">Sinopsis</Label>
                            <Textarea
                                id="synopsis"
                                name="synopsis"
                                placeholder="Isi sinopsis"
                                value={data.synopsis}
                                onChange={onHandleChange}
                            />
                            {errors.synopsis && <InputError message={errors.synopsis} />}
                        </div>

                        <div className="grid grid-cols-1 gap-6 lg:grid-cols-2">
                            <div className="grid w-full items-center gap-1.5">
                                <Label htmlFor="category_id">Kategori</Label>
                                <Select
                                    value={data.category_id}
                                    onValueChange={(value) => setData('category_id', value)}
                                >
                                    <SelectTrigger className="w-full">
                                        <SelectValue placeholder="Pilih Kategori" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {props.page_data.categories?.map((category, index) => (
                                            <SelectItem key={index} value={category.value}>
                                                {category.label}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>

                                {errors.category_id && <InputError message={errors.category_id} />}
                            </div>

                            <div className="grid w-full items-center gap-1.5">
                                <Label htmlFor="publisher_id">Penerbit</Label>
                                <Select
                                    value={data.publisher_id}
                                    onValueChange={(value) => setData('publisher_id', value)}
                                >
                                    <SelectTrigger className="w-full">
                                        <SelectValue placeholder="Pilih Penerbit" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {props.page_data.publishers?.map((publisher, index) => (
                                            <SelectItem key={index} value={publisher.value}>
                                                {publisher.label}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>

                                {errors.publisher_id && <InputError message={errors.publisher_id} />}
                            </div>
                        </div>

                        <div className="grid w-full items-center gap-1.5">
                            <Label htmlFor="cover">Cover</Label>
                            <Input id="cover" name="cover" type="file" onChange={onHandleChange} />
                            {errors.cover && <InputError message={errors.cover} />}
                        </div>

                        <div className="flex justify-end gap-x-2">
                            <Button type="button" variant="ghost" size="lg" onClick={() => reset()}>
                                Reset
                            </Button>
                            <Button type="submit" variant="orange" size="lg" disabled={processing}>
                                Simpan
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    );
}

Create.layout = (page) => <AppLayout title={page.props.page_settings.title}>{page}</AppLayout>;
