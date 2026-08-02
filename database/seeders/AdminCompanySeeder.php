<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminCompanySeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::firstOrCreate([
            'name' => 'PT. Citarasa Kuliner Indonesia',
        ], [
            'description' => 'Payroll management untuk Citra Rasa Kuliner.',
            'phone' => '0877-2983-7101',
            'address' => 'Head Office: Jalan Dalem Kaum 76A, Regol, Kota Bandung 40251 Indonesia. Factory: Jalan Pasir Impun Mandalajati Kota Bandung 40194 Indonesia.',
            'city' => 'Bandung',
            'province' => 'Jawa Barat',
            'country' => 'Indonesia',
        ]);

        User::updateOrCreate([
            'email' => 'admin@example.com',
        ], [
            'company_id' => $company->id,
            'name' => 'Administrator',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::where('role', 'admin')
            ->whereNull('company_id')
            ->update(['company_id' => $company->id]);
    }
}
