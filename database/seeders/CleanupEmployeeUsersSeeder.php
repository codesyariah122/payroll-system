<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CleanupEmployeeUsersSeeder extends Seeder
{
    /**
     * Remove employee login accounts that were created by older employee imports.
     *
     * Employee master data is kept. Only users with role "employee" are deleted,
     * and matching employees.user_id values are detached first.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $employeeUserIds = User::where('role', 'employee')->pluck('id');

            if ($employeeUserIds->isEmpty()) {
                $this->command?->info('Tidak ada user role employee yang perlu dibersihkan.');

                return;
            }

            $detachedEmployees = Employee::whereIn('user_id', $employeeUserIds)->update([
                'user_id' => null,
            ]);

            $deletedUsers = User::whereIn('id', $employeeUserIds)->delete();

            $this->command?->info("Cleanup selesai: {$detachedEmployees} employee dilepas dari user login, {$deletedUsers} user role employee dihapus.");
        });
    }
}
