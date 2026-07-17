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
import { create, destroy, edit, index } from '@/routes/admin/cards';
import type { BankCard, BankOption, CardTypeOption } from '@/types';

type Props = {
    cards: BankCard[];
    banks: BankOption[];
    cardTypes: CardTypeOption[];
    paymentSystems: string[];
    filters: {
        bank_id?: number | null;
        card_type?: string | null;
        payment_system?: string | null;
    };
};

export default function CardsIndex({
    cards,
    banks,
    cardTypes,
    paymentSystems,
    filters,
}: Props) {
    return (
        <>
            <Head title="Карты" />

            <div className="space-y-6">
                <div className="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h1 className="text-2xl font-semibold tracking-tight">
                            Карты
                        </h1>
                        <p className="text-muted-foreground">
                            Каталог банковских карт и модерация публикаций.
                        </p>
                    </div>
                    <Link
                        href={create.url()}
                        className={cn(buttonVariants(), 'gap-1.5')}
                    >
                        <Plus className="size-4" />
                        Добавить карту
                    </Link>
                </div>
                <div className="flex flex-wrap gap-2">
                    <Link
                        href={index.url()}
                        className={cn(
                            buttonVariants({
                                variant:
                                    !filters.bank_id &&
                                    !filters.card_type &&
                                    !filters.payment_system
                                        ? 'default'
                                        : 'outline',
                                size: 'sm',
                            }),
                        )}
                    >
                        Все
                    </Link>
                    {cardTypes.map((type) => (
                        <Link
                            key={type.value}
                            href={index.url({
                                query: { card_type: type.value },
                            })}
                            className={cn(
                                buttonVariants({
                                    variant:
                                        filters.card_type === type.value
                                            ? 'default'
                                            : 'outline',
                                    size: 'sm',
                                }),
                            )}
                        >
                            {type.label}
                        </Link>
                    ))}
                </div>

                <div className="flex flex-wrap gap-3">
                    <select
                        className="border-input bg-background h-9 rounded-md border px-3 text-sm"
                        defaultValue={filters.bank_id ?? ''}
                        onChange={(e) => {
                            const value = e.target.value;
                            router.get(
                                index.url({
                                    query: {
                                        ...(value
                                            ? { bank_id: Number(value) }
                                            : {}),
                                        ...(filters.card_type
                                            ? {
                                                  card_type:
                                                      filters.card_type,
                                              }
                                            : {}),
                                        ...(filters.payment_system
                                            ? {
                                                  payment_system:
                                                      filters.payment_system,
                                              }
                                            : {}),
                                    },
                                }),
                            );
                        }}
                    >
                        <option value="">Все банки</option>
                        {banks.map((bank) => (
                            <option key={bank.id} value={bank.id}>
                                {bank.name}
                            </option>
                        ))}
                    </select>
                    <select
                        className="border-input bg-background h-9 rounded-md border px-3 text-sm"
                        defaultValue={filters.payment_system ?? ''}
                        onChange={(e) => {
                            const value = e.target.value;
                            router.get(
                                index.url({
                                    query: {
                                        ...(filters.bank_id
                                            ? { bank_id: filters.bank_id }
                                            : {}),
                                        ...(filters.card_type
                                            ? {
                                                  card_type:
                                                      filters.card_type,
                                              }
                                            : {}),
                                        ...(value
                                            ? { payment_system: value }
                                            : {}),
                                    },
                                }),
                            );
                        }}
                    >
                        <option value="">Все платёжные системы</option>
                        {paymentSystems.map((system) => (
                            <option key={system} value={system}>
                                {system}
                            </option>
                        ))}
                    </select>
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>Список карт</CardTitle>
                        <CardDescription>Всего: {cards.length}</CardDescription>
                    </CardHeader>
                    <CardContent className="overflow-x-auto">
                        <table className="w-full min-w-[960px] text-left text-sm">
                            <thead>
                                <tr className="border-b border-border text-muted-foreground">
                                    <th className="px-2 py-2 font-medium">
                                        Карта
                                    </th>
                                    <th className="px-2 py-2 font-medium">
                                        Банк
                                    </th>
                                    <th className="px-2 py-2 font-medium">
                                        Платёжная система
                                    </th>
                                    <th className="px-2 py-2 font-medium">
                                        Тип
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
                                {cards.map((card) => (
                                    <tr
                                        key={card.id}
                                        className="border-b border-border/70"
                                    >
                                        <td className="px-2 py-3">
                                            <div className="font-medium">
                                                {card.title}
                                            </div>
                                            <div className="text-xs text-muted-foreground">
                                                {card.slug}
                                            </div>
                                        </td>
                                        <td className="px-2 py-3">
                                            {card.bank_name ?? '—'}
                                        </td>
                                        <td className="px-2 py-3">
                                            {card.payment_system ?? '—'}
                                        </td>
                                        <td className="px-2 py-3">
                                            {card.card_type_label ?? '—'}
                                        </td>
                                        <td className="px-2 py-3">
                                            {card.is_active
                                                ? 'Активна'
                                                : 'Скрыта'}
                                        </td>
                                        <td className="px-2 py-3">
                                            <div className="flex justify-end gap-2">
                                                <Link
                                                    href={edit.url(card.slug)}
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
                                                                `Удалить карту «${card.title}»?`,
                                                            )
                                                        ) {
                                                            router.delete(
                                                                destroy.url(
                                                                    card.slug,
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
                                {cards.length === 0 && (
                                    <tr>
                                        <td
                                            colSpan={6}
                                            className="px-2 py-8 text-center text-muted-foreground"
                                        >
                                            Карт пока нет.{' '}
                                            <Link
                                                href={create.url()}
                                                className="underline"
                                            >
                                                Создать первую
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
