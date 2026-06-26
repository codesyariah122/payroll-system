<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Position;

class PositionSeeder extends Seeder
{
    public function run(): void
    {
        $positions = [
            'Manager',
            'Supervisor',
            'Staff',
            'Admin',
        ];

        foreach ($positions as $position) {
            Position::create([
                'name' => $position
            ]);
        }
    }
}
