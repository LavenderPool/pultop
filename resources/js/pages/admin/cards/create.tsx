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
import { index, store } from '@/routes/admin/cards';
import type { BankOption, CardTypeOption } from '@/types';
import { CardFormFields } from './card-form-fields';

type Props = {
    banks: BankOption[];
    cardTypes: CardTypeOption[];
};

export default function CardsCreate({ banks, cardTypes }: Props) {
    return (
        <>
            <Head title="Новая карта" />

            <div className="mx-auto max-w-3xl space-y-6">
                <div>
                    <h1 className="text-2xl font-semibold tracking-tight">
                        Новая карта
                    </h1>
                    <p className="text-muted-foreground">
                        Карточка банковской карты для публичного каталога.
                    </p>
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>Данные карты</CardTitle>
                        <CardDescription>
                            Особые условия можно вставлять как HTML.
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
                                    <CardFormFields
                                        banks={banks}
                                        cardTypes={cardTypes}
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
