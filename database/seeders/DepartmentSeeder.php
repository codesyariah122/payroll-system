<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Company;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::firstOrCreate(['name' => 'PT. Citarasa Kuliner Indonesia']);

        $departments = [
            'HRD',
            'Finance',
            'IT',
            'Marketing',
            'Operational',
        ];

        foreach ($departments as $department) {
            Department::firstOrCreate([
                'company_id' => $company->id,
                'name' => $department
            ]);
        }
    }
}
