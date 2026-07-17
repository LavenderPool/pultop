<?php

namespace Database\Seeders;

use App\Enums\CreditTypeSlug;
use App\Models\CreditType;
use Illuminate\Database\Seeder;

class CreditTypeSeeder extends Seeder
{
    public function run(): void
    {
        foreach (CreditTypeSlug::cases() as $index => $type) {
            CreditType::query()->updateOrCreate(
                ['slug' => $type->value],
                [
                    'name' => $type->name(),
                    'title' => $type->title(),
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ],
            );
        }
    }
}
