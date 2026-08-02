<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Company;
use App\Models\Position;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $company = Company::firstOrCreate(['name' => 'PT. Citarasa Kuliner Indonesia']);
        $departments = Department::where('company_id', $company->id)->get();
        $positions = Position::where('company_id', $company->id)->get();

        if ($departments->isEmpty() || $positions->isEmpty()) {
            return;
        }

        $special = [
            'name' => 'Puji Ermanto',
            'email' => 'pujiermanto@gmail.com',
            'nip' => 'EMP-00001',
            'phone' => '+62' . $faker->numerify('812########'),
            'address' => $faker->address(),
            'join_date' => $faker->dateTimeBetween('-4 years', 'now')->format('Y-m-d'),
            'basic_salary' => 8000000,
            'allowance' => 1500000,
            'status' => 'active',
            'department_id' => $departments->random()->id,
            'position_id' => $positions->random()->id,
        ];

        $this->createEmployee($special, $company->id);

        for ($i = 2; $i <= 20; $i++) {
            $data = [
                'name' => $faker->name(),
                'email' => 'employee' . str_pad($i, 2, '0', STR_PAD_LEFT) . '@example.com',
                'nip' => 'EMP-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'phone' => '+62' . $faker->numerify('812########'),
                'address' => $faker->address(),
                'join_date' => $faker->dateTimeBetween('-4 years', 'now')->format('Y-m-d'),
                'basic_salary' => $faker->numberBetween(4000000, 18000000),
                'allowance' => $faker->numberBetween(500000, 3000000),
                'status' => $faker->randomElement(['active', 'inactive']),
                'department_id' => $departments->random()->id,
                'position_id' => $positions->random()->id,
            ];

            $this->createEmployee($data, $company->id);
        }
    }

    protected function createEmployee(array $data, int $companyId): void
    {
        $user = User::updateOrCreate([
            'email' => $data['email'],
        ], [
            'company_id' => $companyId,
            'name' => $data['name'],
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => 'employee',
        ]);

        Employee::updateOrCreate([
            'email' => $data['email'],
        ], [
            'company_id' => $companyId,
            'user_id' => $user->id,
            'department_id' => $data['department_id'],
            'position_id' => $data['position_id'],
            'nip' => $data['nip'],
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'join_date' => $data['join_date'],
            'basic_salary' => $data['basic_salary'],
            'allowance' => $data['allowance'],
            'status' => $data['status'],
        ]);
    }
}
