import {
    Building2,
    Coins,
    CreditCard,
    HandCoins,
    LayoutDashboard,
    type LucideIcon,
    Newspaper,
    PiggyBank,
    RefreshCw,
    Settings,
} from 'lucide-react';
import { dashboard } from '@/routes/admin';
import { index as articlesIndex } from '@/routes/admin/articles';
import { index as banksIndex } from '@/routes/admin/banks';
import { index as cardsIndex } from '@/routes/admin/cards';
import { index as creditsIndex } from '@/routes/admin/credits';
import { index as depositsIndex } from '@/routes/admin/deposits';
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
        title: 'Кредиты',
        href: creditsIndex.url(),
        icon: HandCoins,
    },
    {
        title: 'Вклады',
        href: depositsIndex.url(),
        icon: PiggyBank,
    },
    {
        title: 'Карты',
        href: cardsIndex.url(),
        icon: CreditCard,
    },
    {
        title: 'Статьи и новости',
        href: articlesIndex.url(),
        icon: Newspaper,
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
