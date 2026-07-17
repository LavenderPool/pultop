import { Head, Link, usePage } from '@inertiajs/react';
import {
    Building2,
    CreditCard,
    FileText,
    HandCoins,
    Newspaper,
    PiggyBank,
} from 'lucide-react';
import type { ComponentType } from 'react';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { index as articlesIndex } from '@/routes/admin/articles';
import { index as banksIndex } from '@/routes/admin/banks';
import { index as cardsIndex } from '@/routes/admin/cards';
import { index as creditsIndex } from '@/routes/admin/credits';
import { index as depositsIndex } from '@/routes/admin/deposits';
import { edit as goldSettingsEdit } from '@/routes/admin/settings/gold';
import { edit as ratesSettingsEdit } from '@/routes/admin/settings/rates';
import type { SharedData } from '@/types';

type ParseRunSummary = {
    status: string;
    status_label: string;
    ok_count: number;
    fail_count: number;
    finished_at: string | null;
};

type Stats = {
    banks_active: number;
    credits_active: number;
    deposits_active: number;
    cards_active: number;
    articles_published: number;
    articles_draft: number;
    rates_parse: ParseRunSummary | null;
    gold_parse: ParseRunSummary | null;
};

type Props = {
    stats: Stats;
};

type StatTile = {
    title: string;
    value: number;
    href: string;
    icon: ComponentType<{ className?: string }>;
};

function ParseRunCard({
    title,
    href,
    run,
}: {
    title: string;
    href: string;
    run: ParseRunSummary | null;
}) {
    return (
        <Card>
            <CardHeader>
                <CardTitle>
                    <Link href={href} className="hover:underline">
                        {title}
                    </Link>
                </CardTitle>
                <CardDescription>Последний запуск парсера</CardDescription>
            </CardHeader>
            <CardContent className="space-y-1 text-sm">
                {run ? (
                    <>
                        <p>
                            <span className="text-muted-foreground">
                                Статус:{' '}
                            </span>
                            {run.status_label}
                        </p>
                        <p>
                            <span className="text-muted-foreground">
                                Успешно / ошибки:{' '}
                            </span>
                            {run.ok_count} / {run.fail_count}
                        </p>
                        <p>
                            <span className="text-muted-foreground">
                                Завершён:{' '}
                            </span>
                            {run.finished_at ?? 'ещё выполняется'}
                        </p>
                    </>
                ) : (
                    <p className="text-muted-foreground">
                        Ещё не запускался
                    </p>
                )}
            </CardContent>
        </Card>
    );
}

export default function Dashboard({ stats }: Props) {
    const { auth } = usePage<SharedData>().props;

    const tiles: StatTile[] = [
        {
            title: 'Активные банки',
            value: stats.banks_active,
            href: banksIndex.url(),
            icon: Building2,
        },
        {
            title: 'Активные кредиты',
            value: stats.credits_active,
            href: creditsIndex.url(),
            icon: HandCoins,
        },
        {
            title: 'Активные вклады',
            value: stats.deposits_active,
            href: depositsIndex.url(),
            icon: PiggyBank,
        },
        {
            title: 'Активные карты',
            value: stats.cards_active,
            href: cardsIndex.url(),
            icon: CreditCard,
        },
        {
            title: 'Опубликованные статьи',
            value: stats.articles_published,
            href: articlesIndex.url(),
            icon: Newspaper,
        },
        {
            title: 'Черновики статей',
            value: stats.articles_draft,
            href: articlesIndex.url(),
            icon: FileText,
        },
    ];

    return (
        <>
            <Head title="Дашборд" />

            <div className="space-y-6">
                <div>
                    <h1 className="text-2xl font-semibold tracking-tight">
                        Дашборд
                    </h1>
                    <p className="text-muted-foreground">
                        С возвращением
                        {auth.admin ? `, ${auth.admin.name}` : ''}.
                    </p>
                </div>

                <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    {tiles.map((tile) => {
                        const Icon = tile.icon;

                        return (
                            <Link key={tile.title} href={tile.href}>
                                <Card className="h-full transition-colors hover:bg-muted/40">
                                    <CardHeader className="flex flex-row items-start justify-between space-y-0 pb-2">
                                        <CardTitle className="text-sm font-medium">
                                            {tile.title}
                                        </CardTitle>
                                        <Icon className="size-4 text-muted-foreground" />
                                    </CardHeader>
                                    <CardContent>
                                        <p className="text-3xl font-semibold tracking-tight">
                                            {tile.value}
                                        </p>
                                    </CardContent>
                                </Card>
                            </Link>
                        );
                    })}
                </div>

                <div className="grid gap-4 md:grid-cols-2">
                    <ParseRunCard
                        title="Курсы"
                        href={ratesSettingsEdit.url()}
                        run={stats.rates_parse}
                    />
                    <ParseRunCard
                        title="Золото"
                        href={goldSettingsEdit.url()}
                        run={stats.gold_parse}
                    />
                </div>
            </div>
        </>
    );
}
