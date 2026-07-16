<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'rates_parse_enabled' => '1',
            'rates_parse_cron' => '0 * * * *',
            'rates_parse_concurrency' => '5',
            'rates_parse_delay_ms' => '300',
        ];

        foreach ($defaults as $key => $value) {
            Setting::query()->firstOrCreate(
                ['key' => $key],
                ['value' => $value],
            );
        }
    }
}
