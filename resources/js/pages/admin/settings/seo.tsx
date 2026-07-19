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
import { update } from '@/routes/admin/settings/seo';

type SeoPageRow = {
    key: string;
    label: string;
    title: string;
    description: string;
    keywords: string;
    h1: string;
};

type Props = {
    pages: SeoPageRow[];
};

export default function SeoSettings({ pages }: Props) {
    return (
        <>
            <Head title="SEO" />

            <div className="space-y-6">
                <div>
                    <h1 className="text-2xl font-semibold tracking-tight">
                        SEO
                    </h1>
                    <p className="text-muted-foreground">
                        Title, description, keywords и H1 для типовых страниц
                        сайта. Пустые поля на сайте подставляют значения по
                        умолчанию.
                    </p>
                </div>

                <Form {...update.form()} className="space-y-4">
                    {({ errors: formErrors, processing }) => (
                        <>
                            {pages.map((page, index) => (
                                <Card key={page.key}>
                                    <CardHeader>
                                        <CardTitle>{page.label}</CardTitle>
                                        <CardDescription>
                                            Ключ: {page.key}
                                        </CardDescription>
                                    </CardHeader>
                                    <CardContent className="flex flex-col gap-4">
                                        <input
                                            type="hidden"
                                            name={`pages[${index}][key]`}
                                            value={page.key}
                                        />

                                        <div className="flex flex-col gap-2">
                                            <Label
                                                htmlFor={`pages-${index}-title`}
                                            >
                                                Title
                                            </Label>
                                            <Input
                                                id={`pages-${index}-title`}
                                                name={`pages[${index}][title]`}
                                                defaultValue={page.title}
                                            />
                                            {formErrors[
                                                `pages.${index}.title`
                                            ] && (
                                                <p className="text-sm text-destructive">
                                                    {
                                                        formErrors[
                                                            `pages.${index}.title`
                                                        ]
                                                    }
                                                </p>
                                            )}
                                        </div>

                                        <div className="flex flex-col gap-2">
                                            <Label
                                                htmlFor={`pages-${index}-description`}
                                            >
                                                Description
                                            </Label>
                                            <textarea
                                                id={`pages-${index}-description`}
                                                name={`pages[${index}][description]`}
                                                rows={3}
                                                defaultValue={page.description}
                                                className="border-input bg-background ring-offset-background placeholder:text-muted-foreground focus-visible:ring-ring flex min-h-[60px] w-full rounded-md border px-3 py-2 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none"
                                            />
                                            {formErrors[
                                                `pages.${index}.description`
                                            ] && (
                                                <p className="text-sm text-destructive">
                                                    {
                                                        formErrors[
                                                            `pages.${index}.description`
                                                        ]
                                                    }
                                                </p>
                                            )}
                                        </div>

                                        <div className="flex flex-col gap-2">
                                            <Label
                                                htmlFor={`pages-${index}-keywords`}
                                            >
                                                Keywords
                                            </Label>
                                            <textarea
                                                id={`pages-${index}-keywords`}
                                                name={`pages[${index}][keywords]`}
                                                rows={2}
                                                defaultValue={page.keywords}
                                                placeholder="ключ1, ключ2, ключ3"
                                                className="border-input bg-background ring-offset-background placeholder:text-muted-foreground focus-visible:ring-ring flex min-h-[60px] w-full rounded-md border px-3 py-2 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none"
                                            />
                                            {formErrors[
                                                `pages.${index}.keywords`
                                            ] && (
                                                <p className="text-sm text-destructive">
                                                    {
                                                        formErrors[
                                                            `pages.${index}.keywords`
                                                        ]
                                                    }
                                                </p>
                                            )}
                                        </div>

                                        <div className="flex flex-col gap-2">
                                            <Label
                                                htmlFor={`pages-${index}-h1`}
                                            >
                                                H1
                                            </Label>
                                            <Input
                                                id={`pages-${index}-h1`}
                                                name={`pages[${index}][h1]`}
                                                defaultValue={page.h1}
                                            />
                                            {formErrors[
                                                `pages.${index}.h1`
                                            ] && (
                                                <p className="text-sm text-destructive">
                                                    {
                                                        formErrors[
                                                            `pages.${index}.h1`
                                                        ]
                                                    }
                                                </p>
                                            )}
                                        </div>
                                    </CardContent>
                                </Card>
                            ))}

                            {formErrors.pages && (
                                <p className="text-sm text-destructive">
                                    {formErrors.pages}
                                </p>
                            )}

                            <Button type="submit" disabled={processing}>
                                {processing ? 'Сохранение…' : 'Сохранить'}
                            </Button>
                        </>
                    )}
                </Form>
            </div>
        </>
    );
}
