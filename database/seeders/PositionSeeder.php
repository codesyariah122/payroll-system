<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Position;
use App\Models\Company;

class PositionSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::firstOrCreate(['name' => 'PT. Citarasa Kuliner Indonesia']);

        $positions = [
            'Manager',
            'Supervisor',
            'Staff',
            'Admin',
        ];

        foreach ($positions as $position) {
            Position::firstOrCreate([
                'company_id' => $company->id,
                'name' => $position
            ]);
        }
    }
}
