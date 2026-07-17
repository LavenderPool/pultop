import { Form, Head } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { update } from '@/routes/admin/settings/general';

type Props = {
    settings: {
        social_facebook_url: string;
        social_x_url: string;
        social_instagram_url: string;
        social_youtube_url: string;
        social_telegram_url: string;
    };
};

const fields = [
    {
        name: 'social_facebook_url' as const,
        label: 'Facebook',
        placeholder: 'https://facebook.com/...',
    },
    {
        name: 'social_x_url' as const,
        label: 'X (Twitter)',
        placeholder: 'https://x.com/...',
    },
    {
        name: 'social_instagram_url' as const,
        label: 'Instagram',
        placeholder: 'https://instagram.com/...',
    },
    {
        name: 'social_youtube_url' as const,
        label: 'YouTube',
        placeholder: 'https://youtube.com/...',
    },
    {
        name: 'social_telegram_url' as const,
        label: 'Telegram',
        placeholder: 'https://t.me/...',
    },
];

export default function GeneralSettings({ settings }: Props) {
    return (
        <>
            <Head title="Общие настройки" />

            <div className="space-y-6">
                <div>
                    <h1 className="text-2xl font-semibold tracking-tight">
                        Общие настройки
                    </h1>
                    <p className="text-muted-foreground">
                        Ссылки на социальные сети для верхней панели сайта.
                        Пустые ссылки не отображаются; если все пустые — блок
                        скрыт.
                    </p>
                </div>
                <Card>
                    <CardHeader>
                        <CardTitle>Социальные сети</CardTitle>
                        <CardDescription>
                            Укажите полные URL (включая https://).
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Form
                            {...update.form()}
                            className="flex max-w-xl flex-col gap-4"
                        >
                            {({ errors: formErrors, processing }) => (
                                <>
                                    {fields.map((field) => (
                                        <div
                                            key={field.name}
                                            className="flex flex-col gap-2"
                                        >
                                            <Label htmlFor={field.name}>
                                                {field.label}
                                            </Label>
                                            <Input
                                                id={field.name}
                                                name={field.name}
                                                type="url"
                                                placeholder={field.placeholder}
                                                defaultValue={
                                                    settings[field.name]
                                                }
                                            />
                                            {formErrors[field.name] && (
                                                <p className="text-sm text-destructive">
                                                    {formErrors[field.name]}
                                                </p>
                                            )}
                                        </div>
                                    ))}

                                    <Button
                                        type="submit"
                                        disabled={processing}
                                    >
                                        {processing
                                            ? 'Сохранение…'
                                            : 'Сохранить'}
                                    </Button>
                                </>
                            )}
                        </Form>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}
