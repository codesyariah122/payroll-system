<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->default('html');
            $table->longText('html_content')->nullable();
            $table->string('pdf_path')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_templates');
    }
};
