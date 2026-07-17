import { Form, Head, Link } from '@inertiajs/react';
import { Button, buttonVariants } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { cn } from '@/lib/utils';
import { index, store } from '@/routes/admin/articles';
import type { ArticleCategoryOption } from '@/types';

type Props = {
    categories: ArticleCategoryOption[];
};

export default function ArticlesCreate({ categories }: Props) {
    return (
        <>
            <Head title="Новый материал" />

            <div className="mx-auto max-w-3xl space-y-6">
                <div>
                    <h1 className="text-2xl font-semibold tracking-tight">
                        Новый материал
                    </h1>
                    <p className="text-muted-foreground">
                        Статья, новость или событие для публичной части.
                    </p>
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>Данные материала</CardTitle>
                        <CardDescription>
                            Текст можно вставлять как HTML.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Form
                            {...store.form()}
                            encType="multipart/form-data"
                            className="flex flex-col gap-4"
                        >
                            {({ errors, processing }) => (
                                <>
                                    <Field
                                        id="title"
                                        label="Заголовок"
                                        error={errors.title}
                                        required
                                    />
                                    <Field
                                        id="slug"
                                        label="Slug"
                                        error={errors.slug}
                                        hint="Если пусто — сгенерируется из заголовка"
                                    />
                                    <div className="flex flex-col gap-2">
                                        <Label htmlFor="category">
                                            Категория
                                        </Label>
                                        <select
                                            id="category"
                                            name="category"
                                            required
                                            defaultValue="article"
                                            className="border-input bg-background ring-offset-background focus-visible:ring-ring flex h-9 w-full rounded-md border px-3 py-1 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none"
                                        >
                                            {categories.map((category) => (
                                                <option
                                                    key={category.value}
                                                    value={category.value}
                                                >
                                                    {category.label}
                                                </option>
                                            ))}
                                        </select>
                                        {errors.category && (
                                            <p className="text-sm text-destructive">
                                                {errors.category}
                                            </p>
                                        )}
                                    </div>
                                    <div className="flex flex-col gap-2">
                                        <Label htmlFor="excerpt">Анонс</Label>
                                        <textarea
                                            id="excerpt"
                                            name="excerpt"
                                            rows={3}
                                            className="border-input bg-background ring-offset-background placeholder:text-muted-foreground focus-visible:ring-ring flex min-h-[80px] w-full rounded-md border px-3 py-2 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none"
                                        />
                                        {errors.excerpt && (
                                            <p className="text-sm text-destructive">
                                                {errors.excerpt}
                                            </p>
                                        )}
                                    </div>
                                    <div className="flex flex-col gap-2">
                                        <Label htmlFor="body">Текст (HTML)</Label>
                                        <textarea
                                            id="body"
                                            name="body"
                                            rows={12}
                                            required
                                            className="border-input bg-background ring-offset-background placeholder:text-muted-foreground focus-visible:ring-ring flex min-h-[200px] w-full rounded-md border px-3 py-2 font-mono text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none"
                                        />
                                        {errors.body && (
                                            <p className="text-sm text-destructive">
                                                {errors.body}
                                            </p>
                                        )}
                                    </div>
                                    <Field
                                        id="meta_title"
                                        label="Meta title"
                                        error={errors.meta_title}
                                    />
                                    <div className="flex flex-col gap-2">
                                        <Label htmlFor="meta_description">
                                            Meta description
                                        </Label>
                                        <textarea
                                            id="meta_description"
                                            name="meta_description"
                                            rows={2}
                                            className="border-input bg-background ring-offset-background placeholder:text-muted-foreground focus-visible:ring-ring flex min-h-[60px] w-full rounded-md border px-3 py-2 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none"
                                        />
                                        {errors.meta_description && (
                                            <p className="text-sm text-destructive">
                                                {errors.meta_description}
                                            </p>
                                        )}
                                    </div>
                                    <Field
                                        id="published_at"
                                        label="Дата публикации"
                                        type="datetime-local"
                                        error={errors.published_at}
                                    />
                                    <div className="flex flex-col gap-2">
                                        <Label htmlFor="cover">Обложка</Label>
                                        <Input
                                            id="cover"
                                            name="cover"
                                            type="file"
                                            accept="image/*"
                                        />
                                        {errors.cover && (
                                            <p className="text-sm text-destructive">
                                                {errors.cover}
                                            </p>
                                        )}
                                    </div>
                                    <label className="flex items-center gap-2 text-sm">
                                        <input
                                            type="hidden"
                                            name="is_published"
                                            value="0"
                                        />
                                        <input
                                            type="checkbox"
                                            name="is_published"
                                            value="1"
                                            defaultChecked
                                            className="size-4 rounded border-input"
                                        />
                                        Опубликован
                                    </label>
                                    <div className="flex gap-2 pt-2">
                                        <Button
                                            type="submit"
                                            disabled={processing}
                                        >
                                            {processing
                                                ? 'Сохранение…'
                                                : 'Создать'}
                                        </Button>
                                        <Link
                                            href={index.url()}
                                            className={cn(
                                                buttonVariants({
                                                    variant: 'outline',
                                                }),
                                            )}
                                        >
                                            Отмена
                                        </Link>
                                    </div>
                                </>
                            )}
                        </Form>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

function Field({
    id,
    label,
    error,
    hint,
    type = 'text',
    defaultValue,
    required,
}: {
    id: string;
    label: string;
    error?: string;
    hint?: string;
    type?: string;
    defaultValue?: string;
    required?: boolean;
}) {
    return (
        <div className="flex flex-col gap-2">
            <Label htmlFor={id}>{label}</Label>
            <Input
                id={id}
                name={id}
                type={type}
                defaultValue={defaultValue}
                required={required}
                aria-invalid={!!error}
            />
            {hint && (
                <p className="text-xs text-muted-foreground">{hint}</p>
            )}
            {error && <p className="text-sm text-destructive">{error}</p>}
        </div>
    );
}
