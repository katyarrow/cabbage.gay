<?php

namespace Database\Seeders;

use App\Enums\AnalyticsTagNames;
use App\Models\AnalyticsTag;
use Illuminate\Database\Seeder;

class AnalyticsTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (AnalyticsTagNames::cases() as $name) {
            AnalyticsTag::updateOrCreate([
                'name' => $name->value,
            ]);
        }
    }
}
