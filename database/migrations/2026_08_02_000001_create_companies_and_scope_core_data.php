<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->default('Indonesia');
            $table->string('tax_id')->nullable();
            $table->timestamps();
        });

        $this->addCompanyColumn('users');
        $this->addCompanyColumn('departments');
        $this->addCompanyColumn('positions');
        $this->addCompanyColumn('employees');
        $this->addCompanyColumn('payrolls');
        $this->addCompanyColumn('payroll_templates');

        $companyId = DB::table('companies')->insertGetId([
            'name' => 'PT. Citarasa Kuliner Indonesia',
            'description' => 'Payroll management untuk Citra Rasa Kuliner.',
            'phone' => '0877-2983-7101',
            'address' => 'Head Office: Jalan Dalem Kaum 76A, Regol, Kota Bandung 40251 Indonesia. Factory: Jalan Pasir Impun Mandalajati Kota Bandung 40194 Indonesia.',
            'city' => 'Bandung',
            'province' => 'Jawa Barat',
            'country' => 'Indonesia',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach (['users', 'departments', 'positions', 'employees', 'payrolls', 'payroll_templates'] as $table) {
            DB::table($table)->whereNull('company_id')->update(['company_id' => $companyId]);
        }
    }

    public function down(): void
    {
        foreach (['payroll_templates', 'payrolls', 'employees', 'positions', 'departments', 'users'] as $table) {
            if (Schema::hasColumn($table, 'company_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropColumn('company_id');
                });
            }
        }

        Schema::dropIfExists('companies');
    }

    private function addCompanyColumn(string $tableName): void
    {
        if (! Schema::hasColumn($tableName, 'company_id')) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->after('id')->index();
            });
        }
    }
};
