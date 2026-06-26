<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('departments')->insert([
            [
                'name' => 'HRD',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Finance',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Kitchen',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Operational',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Marketing',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('positions')->insert([
            [
                'name' => 'Staff',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Supervisor',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Manager',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Operator',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        DB::table('departments')->whereIn('name', [
            'HRD',
            'Finance',
            'Kitchen',
            'Operational',
            'Marketing'
        ])->delete();

        DB::table('positions')->whereIn('name', [
            'Staff',
            'Supervisor',
            'Manager',
            'Admin',
            'Operator'
        ])->delete();
    }
};
