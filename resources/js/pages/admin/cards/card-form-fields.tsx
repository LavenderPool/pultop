import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type {
    BankOption,
    CardConditionRow,
    CardTypeOption,
} from '@/types';

type Props = {
    banks: BankOption[];
    cardTypes: CardTypeOption[];
    errors: Record<string, string>;
    imageUrl?: string | null;
    defaults?: {
        title?: string;
        slug?: string;
        bank_id?: number | null;
        currency?: string;
        payment_system?: string | null;
        card_type?: string | null;
        category?: string | null;
        issue_cost_display?: string | null;
        validity_display?: string | null;
        special_conditions?: string | null;
        apply_url?: string | null;
        is_active?: boolean;
        sort_order?: number;
        conditions?: CardConditionRow[];
    };
};

export function CardFormFields({
    banks,
    cardTypes,
    errors,
    imageUrl,
    defaults = {},
}: Props) {
    const [conditions, setConditions] = useState<CardConditionRow[]>(
        defaults.conditions?.length
            ? defaults.conditions
            : [{ label: '', value: '', note: '' }],
    );

    return (
        <>
            <Field
                id="title"
                label="Название"
                defaultValue={defaults.title ?? ''}
                error={errors.title}
                required
            />
            <Field
                id="slug"
                label="Slug"
                defaultValue={defaults.slug ?? ''}
                error={errors.slug}
                hint="Если пусто — сгенерируется из названия"
            />

            <div className="flex flex-col gap-2">
                <Label htmlFor="bank_id">Банк</Label>
                <select
                    id="bank_id"
                    name="bank_id"
                    defaultValue={defaults.bank_id ?? ''}
                    className="border-input bg-background ring-offset-background focus-visible:ring-ring flex h-9 w-full rounded-md border px-3 py-1 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none"
                >
                    <option value="">— без банка —</option>
                    {banks.map((bank) => (
                        <option key={bank.id} value={bank.id}>
                            {bank.name}
                        </option>
                    ))}
                </select>
                {errors.bank_id && (
                    <p className="text-sm text-destructive">{errors.bank_id}</p>
                )}
            </div>

            <div className="flex flex-col gap-2">
                <Label htmlFor="card_type">Тип карты</Label>
                <select
                    id="card_type"
                    name="card_type"
                    defaultValue={defaults.card_type ?? ''}
                    className="border-input bg-background ring-offset-background focus-visible:ring-ring flex h-9 w-full rounded-md border px-3 py-1 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none"
                >
                    <option value="">— не указан —</option>
                    {cardTypes.map((type) => (
                        <option key={type.value} value={type.value}>
                            {type.label}
                        </option>
                    ))}
                </select>
                {errors.card_type && (
                    <p className="text-sm text-destructive">{errors.card_type}</p>
                )}
            </div>

            <Field
                id="currency"
                label="Валюта"
                defaultValue={defaults.currency ?? 'sum'}
                error={errors.currency}
                hint="Код как в фильтре: sum, usd, eur…"
            />
            <Field
                id="payment_system"
                label="Платёжная система"
                defaultValue={defaults.payment_system ?? ''}
                error={errors.payment_system}
            />
            <Field
                id="category"
                label="Категория"
                defaultValue={defaults.category ?? ''}
                error={errors.category}
            />
            <Field
                id="issue_cost_display"
                label="Стоимость выпуска"
                defaultValue={defaults.issue_cost_display ?? ''}
                error={errors.issue_cost_display}
            />
            <Field
                id="validity_display"
                label="Срок действия"
                defaultValue={defaults.validity_display ?? ''}
                error={errors.validity_display}
            />
            <Field
                id="apply_url"
                label="URL заявки на сайте банка"
                defaultValue={defaults.apply_url ?? ''}
                error={errors.apply_url}
            />
            <Field
                id="sort_order"
                label="Порядок сортировки"
                type="number"
                defaultValue={String(defaults.sort_order ?? 0)}
                error={errors.sort_order}
            />

            <div className="flex flex-col gap-2">
                <Label htmlFor="image">Изображение карты</Label>
                {imageUrl && (
                    <img
                        src={imageUrl}
                        alt=""
                        className="mb-1 h-24 w-auto rounded border border-border object-contain"
                    />
                )}
                <Input id="image" name="image" type="file" accept="image/*" />
                {errors.image && (
                    <p className="text-sm text-destructive">{errors.image}</p>
                )}
            </div>

            <div className="flex flex-col gap-2">
                <Label htmlFor="special_conditions">Особые условия (HTML)</Label>
                <textarea
                    id="special_conditions"
                    name="special_conditions"
                    rows={6}
                    defaultValue={defaults.special_conditions ?? ''}
                    className="border-input bg-background ring-offset-background placeholder:text-muted-foreground focus-visible:ring-ring flex min-h-[80px] w-full rounded-md border px-3 py-2 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none"
                />
                {errors.special_conditions && (
                    <p className="text-sm text-destructive">
                        {errors.special_conditions}
                    </p>
                )}
            </div>

            <div className="space-y-3 rounded-md border border-border p-3">
                <div className="flex items-center justify-between">
                    <Label>Базовые условия</Label>
                    <Button
                        type="button"
                        variant="outline"
                        size="sm"
                        onClick={() =>
                            setConditions((rows) => [
                                ...rows,
                                { label: '', value: '', note: '' },
                            ])
                        }
                    >
                        Добавить условие
                    </Button>
                </div>
                {conditions.map((row, index) => (
                    <div
                        key={index}
                        className="grid gap-2 md:grid-cols-[1fr_1fr_1fr_auto]"
                    >
                        <Input
                            name={`conditions[${index}][label]`}
                            placeholder="Название"
                            defaultValue={row.label}
                        />
                        <Input
                            name={`conditions[${index}][value]`}
                            placeholder="Значение"
                            defaultValue={row.value ?? ''}
                        />
                        <Input
                            name={`conditions[${index}][note]`}
                            placeholder="Примечание"
                            defaultValue={row.note ?? ''}
                        />
                        <Button
                            type="button"
                            variant="ghost"
                            size="sm"
                            onClick={() =>
                                setConditions((rows) =>
                                    rows.filter((_, i) => i !== index),
                                )
                            }
                        >
                            ×
                        </Button>
                    </div>
                ))}
            </div>

            <label className="flex items-center gap-2 text-sm">
                <input type="hidden" name="is_active" value="0" />
                <input
                    type="checkbox"
                    name="is_active"
                    value="1"
                    defaultChecked={defaults.is_active ?? true}
                    className="size-4 rounded border-input"
                />
                Активна (показывать на сайте)
            </label>
        </>
    );
}

function Field({
    id,
    label,
    defaultValue,
    error,
    required,
    hint,
    type = 'text',
}: {
    id: string;
    label: string;
    defaultValue?: string;
    error?: string;
    required?: boolean;
    hint?: string;
    type?: string;
}) {
    return (
        <div className="flex flex-col gap-2">
            <Label htmlFor={id}>
                {label}
                {required ? ' *' : ''}
            </Label>
            <Input
                id={id}
                name={id}
                type={type}
                defaultValue={defaultValue}
                required={required}
            />
            {hint && (
                <p className="text-xs text-muted-foreground">{hint}</p>
            )}
            {error && <p className="text-sm text-destructive">{error}</p>}
        </div>
    );
}
