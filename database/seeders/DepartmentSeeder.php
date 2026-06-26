<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            'HRD',
            'Finance',
            'IT',
            'Marketing',
            'Operational',
        ];

        foreach ($departments as $department) {
            Department::create([
                'name' => $department
            ]);
        }
    }
}
