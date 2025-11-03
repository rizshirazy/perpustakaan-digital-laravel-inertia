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
import { Avatar, AvatarFallback } from '@/Components/ui/avatar';
import { Button } from '@/Components/ui/button';
import { Card, CardContent } from '@/Components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table';
import AppLayout from '@/Layouts/AppLayout';
import { Link } from '@inertiajs/react';
import { IconCategory, IconPencil, IconPlus, IconTrash } from '@tabler/icons-react';

export default function Index(props) {
    const handleDelete = (id) => {
        // Implement delete functionality here
        console.log(`Delete category with id: ${id}`);
    };

    return (
        <div className="flex w-full flex-col pb-32">
            <div className="mb-8 flex flex-col items-start justify-between gap-y-4 lg:flex-row lg:items-center">
                <HeaderTitle
                    title={props.page_settings.title}
                    subtitle={props.page_settings.subtitle}
                    icon={IconCategory}
                />

                <Button variant="orange" size="lg" asChild>
                    <Link href={route('admin.categories.create')}>
                        <IconPlus className="size-4" /> Tambah
                    </Link>
                </Button>
            </div>

            <Card>
                <CardContent className="p-0 [&-td]:whitespace-nowrap [&_td]:px-6 [&_th]:px-6">
                    <Table className="w-full">
                        <TableHeader>
                            <TableRow>
                                <TableHead>#</TableHead>
                                <TableHead>Nama</TableHead>
                                <TableHead>Slug</TableHead>
                                <TableHead>Cover</TableHead>
                                <TableHead>Dibuat Pada</TableHead>
                                <TableHead>Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {props.categories.map((category, index) => (
                                <TableRow key={index}>
                                    <TableCell>{index + 1}</TableCell>
                                    <TableCell>{category.name}</TableCell>
                                    <TableCell>{category.slug}</TableCell>
                                    <TableCell>
                                        <Avatar src={category.cover} alt={category.name}>
                                            <AvatarFallback>{category.name.substring(0, 1)}</AvatarFallback>
                                        </Avatar>
                                    </TableCell>
                                    <TableCell>{new Date(category.created_at).toLocaleDateString()}</TableCell>
                                    <TableCell>
                                        <div className="flex items-center gap-x-1">
                                            <Button variant="blue" size="sm" asChild>
                                                <Link href={route('admin.categories.edit', [category])}>
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
                                                            Apakah anda yakin menghapus kategori {category.name}?
                                                        </AlertDialogTitle>
                                                        <AlertDialogDescription>
                                                            Tindakan ini tidak dapat dibatalkan dan akan menghapus
                                                            kategori beserta seluruh data yang berhubungan dengan
                                                            kategori ini.
                                                        </AlertDialogDescription>
                                                    </AlertDialogHeader>
                                                    <AlertDialogFooter>
                                                        <AlertDialogCancel>Batal</AlertDialogCancel>
                                                        <AlertDialogAction onClick={() => handleDelete(category.id)}>
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
            </Card>
        </div>
    );
}

Index.layout = (page) => <AppLayout title={page.props.page_settings.title}>{page}</AppLayout>;
