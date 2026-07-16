import {
    Building2,
    Coins,
    LayoutDashboard,
    type LucideIcon,
    RefreshCw,
    Settings,
} from 'lucide-react';
import { dashboard } from '@/routes/admin';
import { index as banksIndex } from '@/routes/admin/banks';
import { edit as generalSettingsEdit } from '@/routes/admin/settings/general';
import { edit as goldSettingsEdit } from '@/routes/admin/settings/gold';
import { edit as ratesSettingsEdit } from '@/routes/admin/settings/rates';

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
    {
        title: 'Банки',
        href: banksIndex.url(),
        icon: Building2,
    },
    {
        title: 'Общие настройки',
        href: generalSettingsEdit.url(),
        icon: Settings,
    },
    {
        title: 'Курсы / парсер',
        href: ratesSettingsEdit.url(),
        icon: RefreshCw,
    },
    {
        title: 'Золото / парсер',
        href: goldSettingsEdit.url(),
        icon: Coins,
    },
];
