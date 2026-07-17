import { Form, Head, Link } from '@inertiajs/react';
import { Button, buttonVariants } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { cn } from '@/lib/utils';
import { index, update } from '@/routes/admin/deposits';
import type { BankOption, Deposit } from '@/types';
import { DepositFormFields } from './deposit-form-fields';

type Props = {
    deposit: Deposit;
    banks: BankOption[];
    currencies: string[];
};

export default function DepositsEdit({ deposit, banks, currencies }: Props) {
    return (
        <>
            <Head title={`Вклад: ${deposit.title}`} />

            <div className="mx-auto max-w-3xl space-y-6">
                <div>
                    <h1 className="text-2xl font-semibold tracking-tight">
                        Редактирование вклада
                    </h1>
                    <p className="text-muted-foreground">{deposit.title}</p>
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>Данные вклада</CardTitle>
                        <CardDescription>
                            Изменения применяются сразу после сохранения.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Form
                            {...update.form(deposit.slug)}
                            className="flex flex-col gap-4"
                        >
                            {({ errors, processing }) => (
                                <>
                                    <DepositFormFields
                                        banks={banks}
                                        currencies={currencies}
                                        errors={errors}
                                        defaults={{
                                            title: deposit.title,
                                            slug: deposit.slug,
                                            bank_id: deposit.bank_id,
                                            currency: deposit.currency,
                                            rate_display: deposit.rate_display,
                                            term_display: deposit.term_display,
                                            amount_display:
                                                deposit.amount_display,
                                            term_min_months:
                                                deposit.term_min_months,
                                            term_max_months:
                                                deposit.term_max_months,
                                            amount_min: deposit.amount_min,
                                            amount_max: deposit.amount_max,
                                            early_termination:
                                                deposit.early_termination,
                                            partial_withdrawal:
                                                deposit.partial_withdrawal,
                                            capitalization:
                                                deposit.capitalization,
                                            is_online: deposit.is_online,
                                            special_conditions:
                                                deposit.special_conditions,
                                            apply_url: deposit.apply_url,
                                            is_active: deposit.is_active,
                                            sort_order: deposit.sort_order,
                                            rate_rows: deposit.rate_rows,
                                            conditions: deposit.conditions,
                                        }}
                                    />
                                    <div className="flex gap-2 pt-2">
                                        <Button
                                            type="submit"
                                            disabled={processing}
                                        >
                                            Сохранить
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
