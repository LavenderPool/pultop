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
import { create, destroy, edit, index } from '@/routes/admin/credits';
import type { Credit, CreditTypeOption } from '@/types';

type Props = {
    credits: Credit[];
    types: CreditTypeOption[];
    filters: {
        type_id?: number | null;
    };
};

export default function CreditsIndex({ credits, types, filters }: Props) {
    return (
        <>
            <Head title="Кредиты" />

            <div className="space-y-6">
                <div className="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h1 className="text-2xl font-semibold tracking-tight">
                            Кредиты
                        </h1>
                        <p className="text-muted-foreground">
                            Каталог кредитных предложений банков.
                        </p>
                    </div>
                    <Link
                        href={create.url()}
                        className={cn(buttonVariants(), 'gap-1.5')}
                    >
                        <Plus className="size-4" />
                        Добавить кредит
                    </Link>
                </div>
                <div className="flex flex-wrap gap-2">
                    <Link
                        href={index.url()}
                        className={cn(
                            buttonVariants({
                                variant: !filters.type_id
                                    ? 'default'
                                    : 'outline',
                                size: 'sm',
                            }),
                        )}
                    >
                        Все
                    </Link>
                    {types.map((type) => (
                        <Link
                            key={type.id}
                            href={index.url({
                                query: { type_id: type.id },
                            })}
                            className={cn(
                                buttonVariants({
                                    variant:
                                        filters.type_id === type.id
                                            ? 'default'
                                            : 'outline',
                                    size: 'sm',
                                }),
                            )}
                        >
                            {type.name}
                        </Link>
                    ))}
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>Список кредитов</CardTitle>
                        <CardDescription>
                            Всего: {credits.length}
                        </CardDescription>
                    </CardHeader>
                    <CardContent className="overflow-x-auto">
                        <table className="w-full min-w-[960px] text-left text-sm">
                            <thead>
                                <tr className="border-b border-border text-muted-foreground">
                                    <th className="px-2 py-2 font-medium">
                                        Кредит
                                    </th>
                                    <th className="px-2 py-2 font-medium">
                                        Банк
                                    </th>
                                    <th className="px-2 py-2 font-medium">
                                        Ставка
                                    </th>
                                    <th className="px-2 py-2 font-medium">
                                        Типы
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
                                {credits.map((credit) => (
                                    <tr
                                        key={credit.id}
                                        className="border-b border-border/70"
                                    >
                                        <td className="px-2 py-3">
                                            <div className="font-medium">
                                                {credit.title}
                                            </div>
                                            <div className="text-xs text-muted-foreground">
                                                {credit.slug}
                                            </div>
                                        </td>
                                        <td className="px-2 py-3">
                                            {credit.bank_name ?? '—'}
                                        </td>
                                        <td className="px-2 py-3">
                                            {credit.rate_display ?? '—'}
                                        </td>
                                        <td className="px-2 py-3">
                                            {credit.type_names.length > 0
                                                ? credit.type_names.join(', ')
                                                : '—'}
                                        </td>
                                        <td className="px-2 py-3">
                                            {credit.is_active
                                                ? 'Активен'
                                                : 'Скрыт'}
                                        </td>
                                        <td className="px-2 py-3">
                                            <div className="flex justify-end gap-2">
                                                <Link
                                                    href={edit.url(
                                                        credit.slug,
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
                                                                `Удалить кредит «${credit.title}»?`,
                                                            )
                                                        ) {
                                                            router.delete(
                                                                destroy.url(
                                                                    credit.slug,
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
                                {credits.length === 0 && (
                                    <tr>
                                        <td
                                            colSpan={6}
                                            className="px-2 py-8 text-center text-muted-foreground"
                                        >
                                            Кредитов пока нет.{' '}
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
