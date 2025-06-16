<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('uid')->unique();
            $table->string('employee_id')->unique();
            $table->string('work_location');
            $table->string('position');
            $table->string('name');
            $table->integer('age')->nullable();
            $table->string('ssn')->nullable();
            $table->date('join_date')->nullable();
            $table->string('join_date_str')->nullable();
            $table->string('service_period')->nullable();
            $table->string('contact')->nullable();
            $table->decimal('base_salary', 15, 2)->nullable();
            $table->string('employment_status_key');
            $table->string('employment_status_subtext')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};

// Duplicate migration removed to avoid table conflict. All employee fields are now defined in 2025_06_10_000000_create_employees_table.php
