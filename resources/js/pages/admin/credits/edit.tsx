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
import { index, update } from '@/routes/admin/credits';
import type { BankOption, Credit, CreditTypeOption } from '@/types';
import { CreditFormFields } from './credit-form-fields';

type Props = {
    credit: Credit;
    banks: BankOption[];
    types: CreditTypeOption[];
};

export default function CreditsEdit({ credit, banks, types }: Props) {
    return (
        <>
            <Head title={`Кредит: ${credit.title}`} />

            <div className="mx-auto max-w-3xl space-y-6">
                <div>
                    <h1 className="text-2xl font-semibold tracking-tight">
                        Редактирование кредита
                    </h1>
                    <p className="text-muted-foreground">{credit.title}</p>
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>Данные кредита</CardTitle>
                        <CardDescription>
                            Изменения применяются сразу после сохранения.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Form
                            {...update.form(credit.slug)}
                            className="flex flex-col gap-4"
                        >
                            {({ errors, processing }) => (
                                <>
                                    <CreditFormFields
                                        banks={banks}
                                        types={types}
                                        errors={errors}
                                        defaults={{
                                            title: credit.title,
                                            slug: credit.slug,
                                            bank_id: credit.bank_id,
                                            currency: credit.currency,
                                            rate_display: credit.rate_display,
                                            term_display: credit.term_display,
                                            amount_display:
                                                credit.amount_display,
                                            down_payment: credit.down_payment,
                                            special_conditions:
                                                credit.special_conditions,
                                            apply_url: credit.apply_url,
                                            is_active: credit.is_active,
                                            sort_order: credit.sort_order,
                                            type_ids: credit.type_ids,
                                            rate_rows: credit.rate_rows,
                                            conditions: credit.conditions,
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
