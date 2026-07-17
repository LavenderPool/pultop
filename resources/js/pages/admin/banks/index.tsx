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
import { create, destroy, edit } from '@/routes/admin/banks';
import type { Bank } from '@/types';

type Props = {
    banks: Bank[];
};

export default function BanksIndex({ banks }: Props) {
    return (
        <>
            <Head title="Банки" />

            <div className="space-y-6">
                <div className="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h1 className="text-2xl font-semibold tracking-tight">
                            Банки
                        </h1>
                        <p className="text-muted-foreground">
                            Управление банками и привязкой парсеров курсов.
                        </p>
                    </div>
                    <Link
                        href={create.url()}
                        className={cn(buttonVariants(), 'gap-1.5')}
                    >
                        <Plus className="size-4" />
                        Добавить банк
                    </Link>
                </div>
                <Card>
                    <CardHeader>
                        <CardTitle>Список банков</CardTitle>
                        <CardDescription>
                            Всего: {banks.length}
                        </CardDescription>
                    </CardHeader>
                    <CardContent className="overflow-x-auto">
                        <table className="w-full min-w-[720px] text-left text-sm">
                            <thead>
                                <tr className="border-b border-border text-muted-foreground">
                                    <th className="px-2 py-2 font-medium">
                                        Банк
                                    </th>
                                    <th className="px-2 py-2 font-medium">
                                        Slug
                                    </th>
                                    <th className="px-2 py-2 font-medium">
                                        Парсер
                                    </th>
                                    <th className="px-2 py-2 font-medium">
                                        Статус
                                    </th>
                                    <th className="px-2 py-2 font-medium">
                                        Порядок
                                    </th>
                                    <th className="px-2 py-2 font-medium text-right">
                                        Действия
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                {banks.map((bank) => (
                                    <tr
                                        key={bank.id}
                                        className="border-b border-border/70"
                                    >
                                        <td className="px-2 py-3">
                                            <div className="flex items-center gap-2">
                                                {bank.logo_url ? (
                                                    <img
                                                        src={bank.logo_url}
                                                        alt=""
                                                        className="size-8 rounded object-contain"
                                                    />
                                                ) : (
                                                    <div className="size-8 rounded bg-muted" />
                                                )}
                                                <span className="font-medium">
                                                    {bank.name}
                                                </span>
                                            </div>
                                        </td>
                                        <td className="px-2 py-3 text-muted-foreground">
                                            {bank.slug}
                                        </td>
                                        <td className="px-2 py-3">
                                            {bank.parser_code ?? '—'}
                                        </td>
                                        <td className="px-2 py-3">
                                            {bank.is_active
                                                ? 'Активен'
                                                : 'Выключен'}
                                        </td>
                                        <td className="px-2 py-3">
                                            {bank.sort_order}
                                        </td>
                                        <td className="px-2 py-3">
                                            <div className="flex justify-end gap-2">
                                                <Link
                                                    href={edit.url(bank.slug)}
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
                                                                `Удалить банк «${bank.name}»?`,
                                                            )
                                                        ) {
                                                            router.delete(
                                                                destroy.url(
                                                                    bank.slug,
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
                                {banks.length === 0 && (
                                    <tr>
                                        <td
                                            colSpan={6}
                                            className="px-2 py-8 text-center text-muted-foreground"
                                        >
                                            Банков пока нет.{' '}
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
