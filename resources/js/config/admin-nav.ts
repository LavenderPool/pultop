import { LayoutDashboard, type LucideIcon } from 'lucide-react';
import { dashboard } from '@/routes/admin';

export type AdminNavItem = {
    title: string;
    href: string;
    icon: LucideIcon;
};

export const adminNav: AdminNavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard.url(),
        icon: LayoutDashboard,
    },
];
