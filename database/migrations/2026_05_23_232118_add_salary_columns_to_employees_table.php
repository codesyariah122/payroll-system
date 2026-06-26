<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {

            $table->double('basic_salary')->default(0)->after('email');

            $table->double('allowance')->default(0)->after('basic_salary');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {

            $table->dropColumn([
                'basic_salary',
                'allowance'
            ]);
        });
    }
};
