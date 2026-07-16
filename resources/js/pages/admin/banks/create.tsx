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
import { index, store } from '@/routes/admin/banks';

type Props = {
    parserCodes: string[];
};

export default function BanksCreate({ parserCodes }: Props) {
    return (
        <>
            <Head title="Новый банк" />

            <div className="mx-auto max-w-2xl space-y-6">
                <div>
                    <h1 className="text-2xl font-semibold tracking-tight">
                        Новый банк
                    </h1>
                    <p className="text-muted-foreground">
                        Добавление банка для отображения курсов.
                    </p>
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>Данные банка</CardTitle>
                        <CardDescription>
                            Поля с парсером нужны для автоматического обновления
                            курсов.
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
                                        id="name"
                                        label="Название"
                                        error={errors.name}
                                        required
                                    />
                                    <Field
                                        id="slug"
                                        label="Slug"
                                        error={errors.slug}
                                        hint="Если пусто — сгенерируется из названия"
                                    />
                                    <Field
                                        id="address"
                                        label="Адрес"
                                        error={errors.address}
                                    />
                                    <div className="flex flex-col gap-2">
                                        <Label htmlFor="description">
                                            Описание
                                        </Label>
                                        <textarea
                                            id="description"
                                            name="description"
                                            rows={3}
                                            className="border-input bg-background ring-offset-background placeholder:text-muted-foreground focus-visible:ring-ring flex min-h-[80px] w-full rounded-md border px-3 py-2 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                                        />
                                        {errors.description && (
                                            <p className="text-sm text-destructive">
                                                {errors.description}
                                            </p>
                                        )}
                                    </div>
                                    <Field
                                        id="website"
                                        label="Сайт"
                                        type="url"
                                        error={errors.website}
                                    />
                                    <Field
                                        id="license"
                                        label="Лицензия"
                                        error={errors.license}
                                    />
                                    <Field
                                        id="mfo"
                                        label="МФО"
                                        error={errors.mfo}
                                    />
                                    <Field
                                        id="inn"
                                        label="ИНН"
                                        error={errors.inn}
                                    />
                                    <div className="flex flex-col gap-2">
                                        <Label htmlFor="parser_code">
                                            Код парсера
                                        </Label>
                                        <Input
                                            id="parser_code"
                                            name="parser_code"
                                            list="parser-codes"
                                            placeholder="nbu"
                                        />
                                        <datalist id="parser-codes">
                                            {parserCodes.map((code) => (
                                                <option
                                                    key={code}
                                                    value={code}
                                                />
                                            ))}
                                        </datalist>
                                        {errors.parser_code && (
                                            <p className="text-sm text-destructive">
                                                {errors.parser_code}
                                            </p>
                                        )}
                                    </div>
                                    <Field
                                        id="rates_url"
                                        label="URL источника курсов"
                                        type="url"
                                        error={errors.rates_url}
                                    />
                                    <Field
                                        id="sort_order"
                                        label="Порядок"
                                        type="number"
                                        defaultValue="0"
                                        error={errors.sort_order}
                                    />
                                    <div className="flex flex-col gap-2">
                                        <Label htmlFor="logo">Логотип</Label>
                                        <Input
                                            id="logo"
                                            name="logo"
                                            type="file"
                                            accept="image/*"
                                        />
                                        {errors.logo && (
                                            <p className="text-sm text-destructive">
                                                {errors.logo}
                                            </p>
                                        )}
                                    </div>
                                    <label className="flex items-center gap-2 text-sm">
                                        <input
                                            type="hidden"
                                            name="is_active"
                                            value="0"
                                        />
                                        <input
                                            type="checkbox"
                                            name="is_active"
                                            value="1"
                                            defaultChecked
                                            className="size-4 rounded border-input"
                                        />
                                        Активен
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
