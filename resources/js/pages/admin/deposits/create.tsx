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
import { index, store } from '@/routes/admin/deposits';
import type { BankOption } from '@/types';
import { DepositFormFields } from './deposit-form-fields';

type Props = {
    banks: BankOption[];
    currencies: string[];
};

export default function DepositsCreate({ banks, currencies }: Props) {
    return (
        <>
            <Head title="Новый вклад" />

            <div className="mx-auto max-w-3xl space-y-6">
                <div>
                    <h1 className="text-2xl font-semibold tracking-tight">
                        Новый вклад
                    </h1>
                    <p className="text-muted-foreground">
                        Карточка депозитного предложения для публичного каталога.
                    </p>
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>Данные вклада</CardTitle>
                        <CardDescription>
                            Особые условия можно вставлять как HTML.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Form
                            {...store.form()}
                            className="flex flex-col gap-4"
                        >
                            {({ errors, processing }) => (
                                <>
                                    <DepositFormFields
                                        banks={banks}
                                        currencies={currencies}
                                        errors={errors}
                                    />
                                    <div className="flex gap-2 pt-2">
                                        <Button
                                            type="submit"
                                            disabled={processing}
                                        >
                                            Создать
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
