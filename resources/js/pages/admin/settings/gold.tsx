import { Form, Head, router } from '@inertiajs/react';
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
import { run, update } from '@/routes/admin/settings/gold';

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

type Props = {
    settings: {
        gold_parse_enabled: boolean;
        gold_parse_cron: string;
        gold_parse_delay_ms: number;
    };
    cronPresets: CronPreset[];
    runs: ParseRun[];
};

export default function GoldSettings({ settings, cronPresets, runs }: Props) {
    return (
        <>
            <Head title="Золото / парсер" />

            <div className="space-y-6">
                <div className="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h1 className="text-2xl font-semibold tracking-tight">
                            Золото / парсер
                        </h1>
                        <p className="text-muted-foreground">
                            Расписание и параметры импорта цен золота и мест
                            продаж.
                        </p>
                    </div>
                    <Button
                        type="button"
                        variant="outline"
                        onClick={() => {
                            if (confirm('Запустить парсинг золота сейчас?')) {
                                router.post(run.url());
                            }
                        }}
                    >
                        Запустить сейчас
                    </Button>
                </div>
                <Card>
                    <CardHeader>
                        <CardTitle>Настройки</CardTitle>
                        <CardDescription>
                            Системный cron должен вызывать{' '}
                            <code className="text-xs">
                                php artisan schedule:run
                            </code>{' '}
                            каждую минуту. Команда:{' '}
                            <code className="text-xs">php artisan gold:parse</code>
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
                                            name="gold_parse_enabled"
                                            value="0"
                                        />
                                        <input
                                            type="checkbox"
                                            name="gold_parse_enabled"
                                            value="1"
                                            defaultChecked={
                                                settings.gold_parse_enabled
                                            }
                                            className="size-4 rounded border-input"
                                        />
                                        Парсер включён
                                    </label>
                                    {formErrors.gold_parse_enabled && (
                                        <p className="text-sm text-destructive">
                                            {formErrors.gold_parse_enabled}
                                        </p>
                                    )}

                                    <div className="flex flex-col gap-2">
                                        <Label htmlFor="gold_parse_cron">
                                            Расписание
                                        </Label>
                                        <select
                                            id="gold_parse_cron"
                                            name="gold_parse_cron"
                                            defaultValue={
                                                settings.gold_parse_cron
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
                                        {formErrors.gold_parse_cron && (
                                            <p className="text-sm text-destructive">
                                                {formErrors.gold_parse_cron}
                                            </p>
                                        )}
                                    </div>

                                    <div className="flex max-w-xs flex-col gap-2">
                                        <Label htmlFor="gold_parse_delay_ms">
                                            Delay между запросами (мс)
                                        </Label>
                                        <Input
                                            id="gold_parse_delay_ms"
                                            name="gold_parse_delay_ms"
                                            type="number"
                                            min={0}
                                            max={10000}
                                            defaultValue={
                                                settings.gold_parse_delay_ms
                                            }
                                        />
                                        {formErrors.gold_parse_delay_ms && (
                                            <p className="text-sm text-destructive">
                                                {formErrors.gold_parse_delay_ms}
                                            </p>
                                        )}
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
                        <CardTitle>Последние прогоны</CardTitle>
                        <CardDescription>
                            Журнал запусков парсера золота
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
