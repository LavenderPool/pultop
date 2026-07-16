<?php

namespace Database\Seeders;

use App\Models\Bank;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BankSeeder extends Seeder
{
    public function run(): void
    {
        $banks = [
            ['name' => 'NBU', 'sort_order' => 1],
            ['name' => 'KapitalBank', 'sort_order' => 2],
            ['name' => 'Asia Alliance Bank', 'sort_order' => 3],
            ['name' => 'Davr Bank', 'sort_order' => 4],
            ['name' => 'HamkorBank', 'sort_order' => 5],
            ['name' => 'Ipak Yuli Bank', 'sort_order' => 6],
            ['name' => 'Invest Finance Bank', 'sort_order' => 7],
            ['name' => 'Aloqa Bank', 'sort_order' => 8],
            ['name' => 'Asaka Bank', 'sort_order' => 9],
            ['name' => 'Agro Bank', 'sort_order' => 10],
            ['name' => 'BRB', 'sort_order' => 11],
            ['name' => 'Ipoteka-Bank', 'sort_order' => 12],
            ['name' => 'Xalq Banki', 'sort_order' => 13],
            ['name' => 'Saderat Bank Tashkent', 'sort_order' => 14],
            ['name' => 'Orient Finans Bank', 'sort_order' => 15],
            ['name' => 'Tenge Bank', 'sort_order' => 16],
            ['name' => 'Anor Bank', 'sort_order' => 17],
            ['name' => 'APEX BANK', 'sort_order' => 18],
            ['name' => 'HayotBank', 'sort_order' => 19],
            ['name' => 'MikrokreditBank', 'sort_order' => 20],
            ['name' => 'MyBank (Madad)', 'sort_order' => 21],
            ['name' => 'OctoBank', 'sort_order' => 22],
            ['name' => 'Poytaxt Bank', 'sort_order' => 23],
            ['name' => 'SanoatQurilishBank', 'sort_order' => 24],
            ['name' => 'TuronBank', 'sort_order' => 25],
            ['name' => 'Ziraat Bank', 'sort_order' => 26],
            ['name' => 'Garant bank', 'sort_order' => 27],
            ['name' => 'TrastBank', 'sort_order' => 28],
            ['name' => 'UniversalBank', 'sort_order' => 29],
            ['name' => 'KDB Bank Uzbekistan', 'sort_order' => 30],
        ];

        foreach ($banks as $bank) {
            $slug = Str::slug($bank['name']);

            Bank::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $bank['name'],
                    'parser_code' => null,
                    'rates_url' => null,
                    'is_active' => true,
                    'sort_order' => $bank['sort_order'],
                ],
            );
        }
    }
}
