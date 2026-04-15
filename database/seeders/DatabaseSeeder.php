<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CodeSeeder::class,
            TenantSetupSeeder::class,
            IndustryCodeMappingSeeder::class,
            AreaSeeder::class,
        ]);
    }
}
