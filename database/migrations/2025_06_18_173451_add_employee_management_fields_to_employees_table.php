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
        Schema::table('employees', function (Blueprint $table) {
            // Add new fields for employee management
            $table->string('company_name')->nullable()->after('work_location');
            $table->date('date_of_birth')->nullable()->after('age');
            $table->string('resident_registration_number')->nullable()->after('ssn');
            $table->string('contact_number')->nullable()->after('contact');
            $table->date('date_of_joining')->nullable()->after('join_date');
            $table->string('employment_duration')->nullable()->after('service_period');
            $table->integer('work_days')->nullable()->after('employment_duration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'company_name',
                'date_of_birth', 
                'resident_registration_number',
                'contact_number',
                'date_of_joining',
                'employment_duration',
                'work_days'
            ]);
        });
    }
};
