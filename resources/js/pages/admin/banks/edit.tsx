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
import { index, update } from '@/routes/admin/banks';
import type { Bank } from '@/types';

type Props = {
    bank: Bank;
    parserCodes: string[];
};

export default function BanksEdit({ bank, parserCodes }: Props) {
    return (
        <>
            <Head title={`Банк: ${bank.name}`} />

            <div className="mx-auto max-w-2xl space-y-6">
                <div>
                    <h1 className="text-2xl font-semibold tracking-tight">
                        Редактирование банка
                    </h1>
                    <p className="text-muted-foreground">{bank.name}</p>
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>Данные банка</CardTitle>
                        <CardDescription>
                            Изменения применяются сразу после сохранения.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Form
                            {...update.form(bank.slug)}
                            encType="multipart/form-data"
                            className="flex flex-col gap-4"
                        >
                            {({ errors, processing }) => (
                                <>
                                    <Field
                                        id="name"
                                        label="Название"
                                        defaultValue={bank.name}
                                        error={errors.name}
                                        required
                                    />
                                    <Field
                                        id="slug"
                                        label="Slug"
                                        defaultValue={bank.slug}
                                        error={errors.slug}
                                    />
                                    <Field
                                        id="website"
                                        label="Сайт"
                                        type="url"
                                        defaultValue={bank.website ?? ''}
                                        error={errors.website}
                                    />
                                    <div className="flex flex-col gap-2">
                                        <Label htmlFor="parser_code">
                                            Код парсера
                                        </Label>
                                        <Input
                                            id="parser_code"
                                            name="parser_code"
                                            list="parser-codes"
                                            defaultValue={
                                                bank.parser_code ?? ''
                                            }
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
                                        defaultValue={bank.rates_url ?? ''}
                                        error={errors.rates_url}
                                    />
                                    <Field
                                        id="sort_order"
                                        label="Порядок"
                                        type="number"
                                        defaultValue={String(bank.sort_order)}
                                        error={errors.sort_order}
                                    />
                                    <div className="flex flex-col gap-2">
                                        <Label htmlFor="logo">Логотип</Label>
                                        {bank.logo_url && (
                                            <img
                                                src={bank.logo_url}
                                                alt=""
                                                className="mb-1 size-12 rounded object-contain"
                                            />
                                        )}
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
                                            defaultChecked={bank.is_active}
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
                                                : 'Сохранить'}
                                        </Button>
                                        <Link
                                            href={index.url()}
                                            className={cn(
                                                buttonVariants({
                                                    variant: 'outline',
                                                }),
                                            )}
                                        >
                                            Назад
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
    type = 'text',
    defaultValue,
    required,
}: {
    id: string;
    label: string;
    error?: string;
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
            {error && <p className="text-sm text-destructive">{error}</p>}
        </div>
    );
}
