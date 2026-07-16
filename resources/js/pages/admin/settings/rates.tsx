import { Form, Head, router, usePage } from '@inertiajs/react';
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
import { proxies as uploadProxies, run, update } from '@/routes/admin/settings/rates';
import type { SharedData } from '@/types';

type CronPreset = {
    value: string;
    label: string;
};

type ParseRun = {
    id: number;
    status: string;
    status_label: string;
    ok_count: number;
    fail_count: number;
    error_summary: string | null;
    started_at: string | null;
    finished_at: string | null;
};

type ProxyItem = {
    id: number;
    host: string;
    port: number;
    label: string;
};

type Props = {
    settings: {
        rates_parse_enabled: boolean;
        rates_parse_cron: string;
        rates_parse_concurrency: number;
        rates_parse_delay_ms: number;
    };
    cronPresets: CronPreset[];
    proxies: ProxyItem[];
    proxiesCount: number;
    runs: ParseRun[];
};

export default function RateSettings({
    settings,
    cronPresets,
    proxies,
    proxiesCount,
    runs,
}: Props) {
    const { flash, errors } = usePage<SharedData & { errors?: Record<string, string> }>().props;

    return (
        <>
            <Head title="Курсы / парсер" />

            <div className="space-y-6">
                <div className="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h1 className="text-2xl font-semibold tracking-tight">
                            Курсы / парсер
                        </h1>
                        <p className="text-muted-foreground">
                            Расписание, прокси и параметры запросов парсера.
                        </p>
                    </div>
                    <Button
                        type="button"
                        variant="outline"
                        onClick={() => {
                            if (confirm('Запустить парсинг сейчас?')) {
                                router.post(run.url());
                            }
                        }}
                    >
                        Запустить сейчас
                    </Button>
                </div>

                {flash?.success && (
                    <div className="rounded-lg border border-border bg-muted/40 px-4 py-3 text-sm">
                        {flash.success}
                    </div>
                )}

                <Card>
                    <CardHeader>
                        <CardTitle>Настройки</CardTitle>
                        <CardDescription>
                            Системный cron должен вызывать{' '}
                            <code className="text-xs">
                                php artisan schedule:run
                            </code>{' '}
                            каждую минуту.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Form
                            {...update.form()}
                            className="flex max-w-xl flex-col gap-4"
                        >
                            {({ errors: formErrors, processing }) => (
                                <>
                                    <label className="flex items-center gap-2 text-sm">
                                        <input
                                            type="hidden"
                                            name="rates_parse_enabled"
                                            value="0"
                                        />
                                        <input
                                            type="checkbox"
                                            name="rates_parse_enabled"
                                            value="1"
                                            defaultChecked={
                                                settings.rates_parse_enabled
                                            }
                                            className="size-4 rounded border-input"
                                        />
                                        Парсер включён
                                    </label>
                                    {formErrors.rates_parse_enabled && (
                                        <p className="text-sm text-destructive">
                                            {formErrors.rates_parse_enabled}
                                        </p>
                                    )}

                                    <div className="flex flex-col gap-2">
                                        <Label htmlFor="rates_parse_cron">
                                            Расписание
                                        </Label>
                                        <select
                                            id="rates_parse_cron"
                                            name="rates_parse_cron"
                                            defaultValue={
                                                settings.rates_parse_cron
                                            }
                                            className="h-9 rounded-lg border border-input bg-background px-3 text-sm"
                                        >
                                            {cronPresets.map((preset) => (
                                                <option
                                                    key={preset.value}
                                                    value={preset.value}
                                                >
                                                    {preset.label} (
                                                    {preset.value})
                                                </option>
                                            ))}
                                        </select>
                                        {formErrors.rates_parse_cron && (
                                            <p className="text-sm text-destructive">
                                                {formErrors.rates_parse_cron}
                                            </p>
                                        )}
                                    </div>

                                    <div className="grid gap-4 sm:grid-cols-2">
                                        <div className="flex flex-col gap-2">
                                            <Label htmlFor="rates_parse_concurrency">
                                                Одновременных потоков
                                            </Label>
                                            <Input
                                                id="rates_parse_concurrency"
                                                name="rates_parse_concurrency"
                                                type="number"
                                                min={1}
                                                max={20}
                                                defaultValue={
                                                    settings.rates_parse_concurrency
                                                }
                                            />
                                            {formErrors.rates_parse_concurrency && (
                                                <p className="text-sm text-destructive">
                                                    {
                                                        formErrors.rates_parse_concurrency
                                                    }
                                                </p>
                                            )}
                                        </div>
                                        <div className="flex flex-col gap-2">
                                            <Label htmlFor="rates_parse_delay_ms">
                                                Delay между батчами (мс)
                                            </Label>
                                            <Input
                                                id="rates_parse_delay_ms"
                                                name="rates_parse_delay_ms"
                                                type="number"
                                                min={0}
                                                max={10000}
                                                defaultValue={
                                                    settings.rates_parse_delay_ms
                                                }
                                            />
                                            {formErrors.rates_parse_delay_ms && (
                                                <p className="text-sm text-destructive">
                                                    {
                                                        formErrors.rates_parse_delay_ms
                                                    }
                                                </p>
                                            )}
                                        </div>
                                    </div>

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

                <Card>
                    <CardHeader>
                        <CardTitle>Прокси</CardTitle>
                        <CardDescription>
                            Формат: host:port:user:password (или host:port:user
                            / host:port). Загрузка полностью заменяет список.
                            Сейчас: {proxiesCount}
                        </CardDescription>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        <Form
                            {...uploadProxies.form()}
                            className="flex max-w-2xl flex-col gap-3"
                        >
                            {({ errors: formErrors, processing }) => (
                                <>
                                    <textarea
                                        name="proxies_text"
                                        rows={8}
                                        placeholder={
                                            '168.81.65.164:443:user\n168.81.66.244:8000:user:pass'
                                        }
                                        className="min-h-40 w-full rounded-lg border border-input bg-background px-3 py-2 font-mono text-sm"
                                    />
                                    {(formErrors.proxies_text ||
                                        errors?.proxies_text) && (
                                        <p className="text-sm text-destructive">
                                            {formErrors.proxies_text ||
                                                errors?.proxies_text}
                                        </p>
                                    )}
                                    <Button
                                        type="submit"
                                        disabled={processing}
                                    >
                                        {processing
                                            ? 'Загрузка…'
                                            : 'Загрузить прокси'}
                                    </Button>
                                </>
                            )}
                        </Form>

                        {proxies.length > 0 && (
                            <div className="max-h-48 overflow-auto rounded-lg border border-border">
                                <ul className="divide-y divide-border text-sm">
                                    {proxies.map((proxy) => (
                                        <li
                                            key={proxy.id}
                                            className="px-3 py-2 font-mono text-xs"
                                        >
                                            {proxy.label}
                                        </li>
                                    ))}
                                </ul>
                            </div>
                        )}
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Последние прогоны</CardTitle>
                        <CardDescription>
                            Журнал запусков парсера
                        </CardDescription>
                    </CardHeader>
                    <CardContent className="overflow-x-auto">
                        <table className="w-full min-w-[640px] text-left text-sm">
                            <thead>
                                <tr className="border-b border-border text-muted-foreground">
                                    <th className="px-2 py-2 font-medium">
                                        ID
                                    </th>
                                    <th className="px-2 py-2 font-medium">
                                        Статус
                                    </th>
                                    <th className="px-2 py-2 font-medium">
                                        OK / Fail
                                    </th>
                                    <th className="px-2 py-2 font-medium">
                                        Начало
                                    </th>
                                    <th className="px-2 py-2 font-medium">
                                        Конец
                                    </th>
                                    <th className="px-2 py-2 font-medium">
                                        Ошибки
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                {runs.map((runItem) => (
                                    <tr
                                        key={runItem.id}
                                        className="border-b border-border/70 align-top"
                                    >
                                        <td className="px-2 py-3">
                                            {runItem.id}
                                        </td>
                                        <td className="px-2 py-3">
                                            {runItem.status_label}
                                        </td>
                                        <td className="px-2 py-3">
                                            {runItem.ok_count} /{' '}
                                            {runItem.fail_count}
                                        </td>
                                        <td className="px-2 py-3 whitespace-nowrap">
                                            {runItem.started_at ?? '—'}
                                        </td>
                                        <td className="px-2 py-3 whitespace-nowrap">
                                            {runItem.finished_at ?? '—'}
                                        </td>
                                        <td className="max-w-xs px-2 py-3 text-xs text-muted-foreground">
                                            {runItem.error_summary ? (
                                                <pre className="whitespace-pre-wrap font-sans">
                                                    {runItem.error_summary}
                                                </pre>
                                            ) : (
                                                '—'
                                            )}
                                        </td>
                                    </tr>
                                ))}
                                {runs.length === 0 && (
                                    <tr>
                                        <td
                                            colSpan={6}
                                            className="px-2 py-8 text-center text-muted-foreground"
                                        >
                                            Прогонов ещё не было.
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}
