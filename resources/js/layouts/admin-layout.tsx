import { Link, router, usePage } from '@inertiajs/react';
import { ChevronDown, ExternalLink, LogOut, Trash2 } from 'lucide-react';
import type { PropsWithChildren } from 'react';
import { Button, buttonVariants } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { adminNav } from '@/config/admin-nav';
import { cn } from '@/lib/utils';
import { home } from '@/routes';
import { logout } from '@/routes/admin';
import { clear } from '@/routes/admin/cache';
import type { SharedData } from '@/types';

function isNavActive(currentUrl: string, href: string): boolean {
    const path = currentUrl.split('?')[0] ?? currentUrl;

    if (path === href) {
        return true;
    }

    return href !== '/admin' && path.startsWith(`${href}/`);
}

export default function AdminLayout({ children }: PropsWithChildren) {
    const page = usePage<SharedData>();
    const { auth, name, flash } = page.props;
    const currentUrl = page.url;

    return (
        <div className="flex min-h-screen bg-background text-foreground">
            <aside className="flex w-64 shrink-0 flex-col border-r border-sidebar-border bg-sidebar text-sidebar-foreground">
                <div className="flex h-14 shrink-0 items-center border-b border-sidebar-border px-4">
                    <span className="text-sm font-semibold tracking-tight">
                        Админка
                    </span>
                </div>

                <div className="p-3 pb-0">
                    <a
                        href={home.url()}
                        target="_blank"
                        rel="noopener noreferrer"
                        className={cn(
                            buttonVariants({ size: 'sm' }),
                            'w-full gap-1.5',
                        )}
                    >
                        <ExternalLink className="size-3.5" />
                        На сайт
                    </a>
                </div>

                <nav className="flex flex-1 flex-col gap-1 p-3">
                    {adminNav.map((item) => {
                        const Icon = item.icon;
                        const active = isNavActive(currentUrl, item.href);

                        return (
                            <Link
                                key={item.href}
                                href={item.href}
                                className={cn(
                                    'flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition-colors',
                                    active
                                        ? 'bg-sidebar-accent font-medium text-sidebar-accent-foreground'
                                        : 'text-sidebar-foreground/70 hover:bg-sidebar-accent/60 hover:text-sidebar-accent-foreground',
                                )}
                            >
                                <Icon className="size-4 shrink-0" />
                                {item.title}
                            </Link>
                        );
                    })}
                </nav>

                <div className="mt-auto shrink-0 border-t border-sidebar-border p-3">
                    <Button
                        type="button"
                        variant="destructive"
                        size="sm"
                        className="w-full gap-1.5"
                        onClick={() => {
                            if (
                                confirm(
                                    'Очистить весь кеш? Это может временно замедлить сайт.',
                                )
                            ) {
                                router.post(clear.url());
                            }
                        }}
                    >
                        <Trash2 className="size-3.5" />
                        Очистить кеш
                    </Button>
                </div>
            </aside>

            <div className="flex min-w-0 flex-1 flex-col">
                <header className="flex h-14 shrink-0 items-center justify-between border-b border-border px-6">
                    <span className="text-sm font-semibold tracking-tight">
                        {name}
                    </span>

                    {auth.admin && (
                        <DropdownMenu>
                            <DropdownMenuTrigger
                                className={cn(
                                    buttonVariants({
                                        variant: 'ghost',
                                        size: 'sm',
                                    }),
                                    'gap-1.5',
                                )}
                            >
                                <span className="max-w-40 truncate">
                                    {auth.admin.name}
                                </span>
                                <ChevronDown className="size-3.5 opacity-60" />
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end" className="min-w-48">
                                <DropdownMenuGroup>
                                    <DropdownMenuLabel className="font-normal">
                                        <div className="flex flex-col gap-0.5">
                                            <span className="text-sm font-medium text-foreground">
                                                {auth.admin.name}
                                            </span>
                                            <span className="text-xs text-muted-foreground">
                                                {auth.admin.email}
                                            </span>
                                        </div>
                                    </DropdownMenuLabel>
                                </DropdownMenuGroup>
                                <DropdownMenuSeparator />
                                <DropdownMenuItem
                                    variant="destructive"
                                    onClick={() => router.post(logout.url())}
                                >
                                    <LogOut />
                                    Выйти
                                </DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    )}
                </header>

                <main className="flex-1 px-6 py-8">
                    {flash?.success && (
                        <div className="mb-6 rounded-lg border border-border bg-muted/40 px-4 py-3 text-sm">
                            {flash.success}
                        </div>
                    )}
                    {children}
                </main>
            </div>
        </div>
    );
}
