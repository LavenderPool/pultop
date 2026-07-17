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
import { index, update } from '@/routes/admin/cards';
import type { BankCard, BankOption, CardTypeOption } from '@/types';
import { CardFormFields } from './card-form-fields';

type Props = {
    card: BankCard;
    banks: BankOption[];
    cardTypes: CardTypeOption[];
};

export default function CardsEdit({ card, banks, cardTypes }: Props) {
    return (
        <>
            <Head title={`Карта: ${card.title}`} />

            <div className="mx-auto max-w-3xl space-y-6">
                <div>
                    <h1 className="text-2xl font-semibold tracking-tight">
                        Редактирование карты
                    </h1>
                    <p className="text-muted-foreground">{card.title}</p>
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>Данные карты</CardTitle>
                        <CardDescription>
                            Изменения применяются сразу после сохранения.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Form
                            {...update.form(card.slug)}
                            encType="multipart/form-data"
                            className="flex flex-col gap-4"
                        >
                            {({ errors, processing }) => (
                                <>
                                    <CardFormFields
                                        banks={banks}
                                        cardTypes={cardTypes}
                                        errors={errors}
                                        imageUrl={card.image_url}
                                        defaults={{
                                            title: card.title,
                                            slug: card.slug,
                                            bank_id: card.bank_id,
                                            currency: card.currency,
                                            payment_system:
                                                card.payment_system,
                                            card_type: card.card_type,
                                            category: card.category,
                                            issue_cost_display:
                                                card.issue_cost_display,
                                            validity_display:
                                                card.validity_display,
                                            special_conditions:
                                                card.special_conditions,
                                            apply_url: card.apply_url,
                                            is_active: card.is_active,
                                            sort_order: card.sort_order,
                                            conditions: card.conditions,
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
