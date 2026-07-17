import { Head, Link, router } from '@inertiajs/react';
import { Plus } from 'lucide-react';
import { Button, buttonVariants } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { cn } from '@/lib/utils';
import { create, destroy, edit, index } from '@/routes/admin/deposits';
import type { BankOption, Deposit } from '@/types';

type Props = {
    deposits: Deposit[];
    banks: BankOption[];
    filters: {
        bank_id?: number | null;
        is_active?: boolean | null;
    };
};

export default function DepositsIndex({ deposits, banks, filters }: Props) {
    return (
        <>
            <Head title="Вклады" />

            <div className="space-y-6">
                <div className="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h1 className="text-2xl font-semibold tracking-tight">
                            Вклады
                        </h1>
                        <p className="text-muted-foreground">
                            Каталог депозитных предложений банков.
                        </p>
                    </div>
                    <Link
                        href={create.url()}
                        className={cn(buttonVariants(), 'gap-1.5')}
                    >
                        <Plus className="size-4" />
                        Добавить вклад
                    </Link>
                </div>
                <div className="flex flex-wrap gap-2">
                    <Link
                        href={index.url()}
                        className={cn(
                            buttonVariants({
                                variant:
                                    !filters.bank_id &&
                                    filters.is_active == null
                                        ? 'default'
                                        : 'outline',
                                size: 'sm',
                            }),
                        )}
                    >
                        Все
                    </Link>
                    <Link
                        href={index.url({ query: { is_active: 1 } })}
                        className={cn(
                            buttonVariants({
                                variant:
                                    filters.is_active === true
                                        ? 'default'
                                        : 'outline',
                                size: 'sm',
                            }),
                        )}
                    >
                        Активные
                    </Link>
                    <Link
                        href={index.url({ query: { is_active: 0 } })}
                        className={cn(
                            buttonVariants({
                                variant:
                                    filters.is_active === false
                                        ? 'default'
                                        : 'outline',
                                size: 'sm',
                            }),
                        )}
                    >
                        Скрытые
                    </Link>
                    {banks.slice(0, 8).map((bank) => (
                        <Link
                            key={bank.id}
                            href={index.url({
                                query: { bank_id: bank.id },
                            })}
                            className={cn(
                                buttonVariants({
                                    variant:
                                        filters.bank_id === bank.id
                                            ? 'default'
                                            : 'outline',
                                    size: 'sm',
                                }),
                            )}
                        >
                            {bank.name}
                        </Link>
                    ))}
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>Список вкладов</CardTitle>
                        <CardDescription>
                            Всего: {deposits.length}
                        </CardDescription>
                    </CardHeader>
                    <CardContent className="overflow-x-auto">
                        <table className="w-full min-w-[960px] text-left text-sm">
                            <thead>
                                <tr className="border-b border-border text-muted-foreground">
                                    <th className="px-2 py-2 font-medium">
                                        Вклад
                                    </th>
                                    <th className="px-2 py-2 font-medium">
                                        Банк
                                    </th>
                                    <th className="px-2 py-2 font-medium">
                                        Ставка
                                    </th>
                                    <th className="px-2 py-2 font-medium">
                                        Валюта
                                    </th>
                                    <th className="px-2 py-2 font-medium">
                                        Статус
                                    </th>
                                    <th className="px-2 py-2 font-medium text-right">
                                        Действия
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                {deposits.map((deposit) => (
                                    <tr
                                        key={deposit.id}
                                        className="border-b border-border/70"
                                    >
                                        <td className="px-2 py-3">
                                            <div className="font-medium">
                                                {deposit.title}
                                            </div>
                                            <div className="text-xs text-muted-foreground">
                                                {deposit.slug}
                                            </div>
                                        </td>
                                        <td className="px-2 py-3">
                                            {deposit.bank_name ?? '—'}
                                        </td>
                                        <td className="px-2 py-3">
                                            {deposit.rate_display ?? '—'}
                                        </td>
                                        <td className="px-2 py-3">
                                            {deposit.currency}
                                        </td>
                                        <td className="px-2 py-3">
                                            {deposit.is_active
                                                ? 'Активен'
                                                : 'Скрыт'}
                                        </td>
                                        <td className="px-2 py-3">
                                            <div className="flex justify-end gap-2">
                                                <Link
                                                    href={edit.url(
                                                        deposit.slug,
                                                    )}
                                                    className={cn(
                                                        buttonVariants({
                                                            variant: 'outline',
                                                            size: 'sm',
                                                        }),
                                                    )}
                                                >
                                                    Изменить
                                                </Link>
                                                <Button
                                                    type="button"
                                                    variant="destructive"
                                                    size="sm"
                                                    onClick={() => {
                                                        if (
                                                            confirm(
                                                                `Удалить вклад «${deposit.title}»?`,
                                                            )
                                                        ) {
                                                            router.delete(
                                                                destroy.url(
                                                                    deposit.slug,
                                                                ),
                                                            );
                                                        }
                                                    }}
                                                >
                                                    Удалить
                                                </Button>
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                                {deposits.length === 0 && (
                                    <tr>
                                        <td
                                            colSpan={6}
                                            className="px-2 py-8 text-center text-muted-foreground"
                                        >
                                            Вкладов пока нет.{' '}
                                            <Link
                                                href={create.url()}
                                                className="underline"
                                            >
                                                Создать первый
                                            </Link>
                                            .
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}
