import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type {
    BankOption,
    CreditConditionRow,
    CreditRateRow,
    CreditTypeOption,
} from '@/types';

type Props = {
    banks: BankOption[];
    types: CreditTypeOption[];
    errors: Record<string, string>;
    defaults?: {
        title?: string;
        slug?: string;
        bank_id?: number | null;
        currency?: string;
        rate_display?: string | null;
        term_display?: string | null;
        amount_display?: string | null;
        down_payment?: string | null;
        special_conditions?: string | null;
        apply_url?: string | null;
        is_active?: boolean;
        sort_order?: number;
        type_ids?: number[];
        rate_rows?: CreditRateRow[];
        conditions?: CreditConditionRow[];
    };
};

export function CreditFormFields({
    banks,
    types,
    errors,
    defaults = {},
}: Props) {
    const [rateRows, setRateRows] = useState<CreditRateRow[]>(
        defaults.rate_rows?.length
            ? defaults.rate_rows
            : [{ rate: '', term: '', note: '' }],
    );
    const [conditions, setConditions] = useState<CreditConditionRow[]>(
        defaults.conditions?.length
            ? defaults.conditions
            : [{ label: '', value: '' }],
    );
    const selectedTypeIds = new Set(defaults.type_ids ?? []);

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
                <Label>Типы кредита</Label>
                <div className="grid gap-2 sm:grid-cols-2">
                    {types.map((type) => (
                        <label
                            key={type.id}
                            className="flex items-center gap-2 text-sm"
                        >
                            <input
                                type="checkbox"
                                name="type_ids[]"
                                value={type.id}
                                defaultChecked={selectedTypeIds.has(type.id)}
                            />
                            {type.name}
                        </label>
                    ))}
                </div>
                {errors.type_ids && (
                    <p className="text-sm text-destructive">{errors.type_ids}</p>
                )}
            </div>

            <Field
                id="currency"
                label="Валюта"
                defaultValue={defaults.currency ?? 'UZS'}
                error={errors.currency}
            />
            <Field
                id="rate_display"
                label="Ставка (в списке)"
                defaultValue={defaults.rate_display ?? ''}
                error={errors.rate_display}
            />
            <Field
                id="term_display"
                label="Срок (в списке)"
                defaultValue={defaults.term_display ?? ''}
                error={errors.term_display}
            />
            <Field
                id="amount_display"
                label="Сумма (в списке)"
                defaultValue={defaults.amount_display ?? ''}
                error={errors.amount_display}
            />
            <Field
                id="down_payment"
                label="Первоначальный взнос"
                defaultValue={defaults.down_payment ?? ''}
                error={errors.down_payment}
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
                    <Label>Таблица ставок</Label>
                    <Button
                        type="button"
                        variant="outline"
                        size="sm"
                        onClick={() =>
                            setRateRows((rows) => [
                                ...rows,
                                { rate: '', term: '', note: '' },
                            ])
                        }
                    >
                        Добавить строку
                    </Button>
                </div>
                {rateRows.map((row, index) => (
                    <div
                        key={index}
                        className="grid gap-2 md:grid-cols-[1fr_1fr_1fr_auto]"
                    >
                        <Input
                            name={`rate_rows[${index}][rate]`}
                            placeholder="Ставка"
                            defaultValue={row.rate}
                        />
                        <Input
                            name={`rate_rows[${index}][term]`}
                            placeholder="Срок"
                            defaultValue={row.term ?? ''}
                        />
                        <Input
                            name={`rate_rows[${index}][note]`}
                            placeholder="Примечание"
                            defaultValue={row.note ?? ''}
                        />
                        <Button
                            type="button"
                            variant="ghost"
                            size="sm"
                            onClick={() =>
                                setRateRows((rows) =>
                                    rows.filter((_, i) => i !== index),
                                )
                            }
                        >
                            ×
                        </Button>
                    </div>
                ))}
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
                                { label: '', value: '' },
                            ])
                        }
                    >
                        Добавить условие
                    </Button>
                </div>
                {conditions.map((row, index) => (
                    <div
                        key={index}
                        className="grid gap-2 md:grid-cols-[1fr_1fr_auto]"
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
                Активен (показывать на сайте)
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
