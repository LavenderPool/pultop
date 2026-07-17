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
import { create, destroy, edit, index } from '@/routes/admin/articles';
import type { Article, ArticleCategoryOption } from '@/types';

type Props = {
    articles: Article[];
    categories: ArticleCategoryOption[];
    filters: {
        category?: string | null;
    };
};

export default function ArticlesIndex({
    articles,
    categories,
    filters,
}: Props) {
    return (
        <>
            <Head title="Статьи и новости" />

            <div className="space-y-6">
                <div className="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h1 className="text-2xl font-semibold tracking-tight">
                            Статьи и новости
                        </h1>
                        <p className="text-muted-foreground">
                            Материалы для главной, листингов и сайдбара.
                        </p>
                    </div>
                    <Link
                        href={create.url()}
                        className={cn(buttonVariants(), 'gap-1.5')}
                    >
                        <Plus className="size-4" />
                        Добавить материал
                    </Link>
                </div>
                <div className="flex flex-wrap gap-2">
                    <Link
                        href={index.url()}
                        className={cn(
                            buttonVariants({
                                variant: !filters.category
                                    ? 'default'
                                    : 'outline',
                                size: 'sm',
                            }),
                        )}
                    >
                        Все
                    </Link>
                    {categories.map((category) => (
                        <Link
                            key={category.value}
                            href={index.url({
                                query: { category: category.value },
                            })}
                            className={cn(
                                buttonVariants({
                                    variant:
                                        filters.category === category.value
                                            ? 'default'
                                            : 'outline',
                                    size: 'sm',
                                }),
                            )}
                        >
                            {category.label}
                        </Link>
                    ))}
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>Список материалов</CardTitle>
                        <CardDescription>
                            Всего: {articles.length}
                        </CardDescription>
                    </CardHeader>
                    <CardContent className="overflow-x-auto">
                        <table className="w-full min-w-[860px] text-left text-sm">
                            <thead>
                                <tr className="border-b border-border text-muted-foreground">
                                    <th className="px-2 py-2 font-medium">
                                        Материал
                                    </th>
                                    <th className="px-2 py-2 font-medium">
                                        Категория
                                    </th>
                                    <th className="px-2 py-2 font-medium">
                                        Дата
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
                                {articles.map((article) => (
                                    <tr
                                        key={article.id}
                                        className="border-b border-border/70"
                                    >
                                        <td className="px-2 py-3">
                                            <div className="flex items-center gap-2">
                                                {article.cover_url ? (
                                                    <img
                                                        src={article.cover_url}
                                                        alt=""
                                                        className="size-10 rounded object-cover"
                                                    />
                                                ) : (
                                                    <div className="size-10 rounded bg-muted" />
                                                )}
                                                <div>
                                                    <div className="font-medium">
                                                        {article.title}
                                                    </div>
                                                    <div className="text-xs text-muted-foreground">
                                                        {article.slug}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td className="px-2 py-3">
                                            {article.category_label}
                                        </td>
                                        <td className="px-2 py-3 text-muted-foreground">
                                            {article.published_at
                                                ? article.published_at.replace(
                                                      'T',
                                                      ' ',
                                                  )
                                                : '—'}
                                        </td>
                                        <td className="px-2 py-3">
                                            {article.is_published
                                                ? 'Опубликован'
                                                : 'Черновик'}
                                        </td>
                                        <td className="px-2 py-3">
                                            <div className="flex justify-end gap-2">
                                                <Link
                                                    href={edit.url(
                                                        article.slug,
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
                                                                `Удалить материал «${article.title}»?`,
                                                            )
                                                        ) {
                                                            router.delete(
                                                                destroy.url(
                                                                    article.slug,
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
                                {articles.length === 0 && (
                                    <tr>
                                        <td
                                            colSpan={5}
                                            className="px-2 py-8 text-center text-muted-foreground"
                                        >
                                            Материалов пока нет.{' '}
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
