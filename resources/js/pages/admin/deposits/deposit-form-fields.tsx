import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type {
    BankOption,
    DepositConditionRow,
    DepositRateRow,
} from '@/types';

type Props = {
    banks: BankOption[];
    currencies: string[];
    errors: Record<string, string>;
    defaults?: {
        title?: string;
        slug?: string;
        bank_id?: number | null;
        currency?: string;
        rate_display?: string | null;
        term_display?: string | null;
        amount_display?: string | null;
        term_min_months?: number | null;
        term_max_months?: number | null;
        amount_min?: number | null;
        amount_max?: number | null;
        early_termination?: boolean;
        partial_withdrawal?: boolean;
        capitalization?: boolean;
        is_online?: boolean;
        special_conditions?: string | null;
        apply_url?: string | null;
        is_active?: boolean;
        sort_order?: number;
        rate_rows?: DepositRateRow[];
        conditions?: DepositConditionRow[];
    };
};

export function DepositFormFields({
    banks,
    currencies,
    errors,
    defaults = {},
}: Props) {
    const [rateRows, setRateRows] = useState<DepositRateRow[]>(
        defaults.rate_rows?.length
            ? defaults.rate_rows
            : [{ rate: '', term: '', note: '' }],
    );
    const [conditions, setConditions] = useState<DepositConditionRow[]>(
        defaults.conditions?.length
            ? defaults.conditions
            : [{ label: '', value: '' }],
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
                <Label htmlFor="currency">Валюта</Label>
                <select
                    id="currency"
                    name="currency"
                    defaultValue={defaults.currency ?? 'UZS'}
                    className="border-input bg-background ring-offset-background focus-visible:ring-ring flex h-9 w-full rounded-md border px-3 py-1 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none"
                >
                    {currencies.map((code) => (
                        <option key={code} value={code}>
                            {code}
                        </option>
                    ))}
                </select>
                {errors.currency && (
                    <p className="text-sm text-destructive">{errors.currency}</p>
                )}
            </div>

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
            <div className="grid gap-4 sm:grid-cols-3">
                <Field
                    id="term_min_months"
                    label="Срок от (мес.)"
                    type="number"
                    defaultValue={
                        defaults.term_min_months != null
                            ? String(defaults.term_min_months)
                            : ''
                    }
                    error={errors.term_min_months}
                />
                <Field
                    id="term_max_months"
                    label="Срок до (мес.)"
                    type="number"
                    defaultValue={
                        defaults.term_max_months != null
                            ? String(defaults.term_max_months)
                            : ''
                    }
                    error={errors.term_max_months}
                />
                <Field
                    id="amount_min"
                    label="Мин. сумма"
                    type="number"
                    defaultValue={
                        defaults.amount_min != null
                            ? String(defaults.amount_min)
                            : ''
                    }
                    error={errors.amount_min}
                />
                <Field
                    id="amount_max"
                    label="Макс. сумма"
                    type="number"
                    defaultValue={
                        defaults.amount_max != null
                            ? String(defaults.amount_max)
                            : ''
                    }
                    error={errors.amount_max}
                />
            </div>
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

            <div className="grid gap-2 sm:grid-cols-2">
                <FlagCheckbox
                    name="early_termination"
                    label="Досрочное расторжение"
                    defaultChecked={defaults.early_termination ?? false}
                />
                <FlagCheckbox
                    name="partial_withdrawal"
                    label="Частичное снятие"
                    defaultChecked={defaults.partial_withdrawal ?? false}
                />
                <FlagCheckbox
                    name="capitalization"
                    label="Капитализация процентов"
                    defaultChecked={defaults.capitalization ?? false}
                />
                <FlagCheckbox
                    name="is_online"
                    label="On-line"
                    defaultChecked={defaults.is_online ?? false}
                />
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

function FlagCheckbox({
    name,
    label,
    defaultChecked,
}: {
    name: string;
    label: string;
    defaultChecked: boolean;
}) {
    return (
        <label className="flex items-center gap-2 text-sm">
            <input type="hidden" name={name} value="0" />
            <input
                type="checkbox"
                name={name}
                value="1"
                defaultChecked={defaultChecked}
                className="size-4 rounded border-input"
            />
            {label}
        </label>
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
