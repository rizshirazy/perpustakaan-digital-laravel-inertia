import ApplicationLogo from '@/Components/ApplicationLogo';
import Banner from '@/Components/Banner';
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar';
import { Button } from '@/Components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/Components/ui/dropdown-menu';
import { Toaster } from '@/Components/ui/sonner';
import { Head, Link, usePage } from '@inertiajs/react';
import Sidebar from './Partials/Sidebar';
import SidebarResponsive from './Partials/SidebarResponsive';

export default function AppLayout({ title, children }) {
    const auth = usePage().props.auth.user;
    const { url } = usePage();
    const announcement = usePage().props.announcement;

    return (
        <>
            <Head title={title} />
            <Toaster position="top-center" richColors />

            <div className="flex min-h-screen w-full flex-row">
                <div className="hidden w-1/5 border-r lg:block">
                    <div className="flex h-full min-h-screen flex-col gap-2">
                        <div className="flex h-14 items-center border-b px-4 lg:h-[60px] lg:px-6">
                            <ApplicationLogo />
                        </div>
                        <div className="flex-1">
                            {/* Sidebar */}
                            <Sidebar url={url} auth={auth} />
                        </div>
                    </div>
                </div>

                <div className="flex w-full flex-col lg:w-4/5">
                    <header className="flex h-12 items-center justify-between gap-4 border-b px-4 lg:h-[60px] lg:justify-end lg:px-6">
                        {/* Sidebar responsive */}
                        <SidebarResponsive url={url} auth={auth} />
                        {/* Dropdown */}
                        <DropdownMenu>
                            <DropdownMenuTrigger asChild>
                                <Button variant="ghost" className="flex gap-x-2">
                                    <span>Hi, {auth.name}</span>
                                    <Avatar>
                                        <AvatarImage src={auth.avatar} alt={auth.name} />
                                        <AvatarFallback>{auth.name.charAt(0)}</AvatarFallback>
                                    </Avatar>
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end">
                                <DropdownMenuLabel>Akun Saya</DropdownMenuLabel>
                                <DropdownMenuSeparator />
                                <DropdownMenuItem>Profile</DropdownMenuItem>
                                <DropdownMenuItem asChild>
                                    <Link href="#">Logout</Link>
                                </DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </header>

                    <main className="w-full">
                        <div className="relative">
                            <div
                                className="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80"
                                aria-hidden="true"
                            >
                                <div
                                    className="relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-orange-100 to-orange-200 opacity-30 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]"
                                    style={{
                                        clipPath:
                                            'polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)',
                                    }}
                                />
                            </div>
                            <div className="gap-4 p-4 lg:gap-64">
                                {children}
                                {announcement && announcement.is_active == 1 && (
                                    <Banner message={announcement.message} url={announcement.url} />
                                )}
                            </div>
                        </div>
                    </main>
                </div>
            </div>
        </>
    );
}
