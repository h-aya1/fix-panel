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
        Schema::create('employee_summaries', function (Blueprint $table) {
            $table->id();
            $table->integer('no')->nullable(); // No.
            $table->string('employee_id')->nullable(); // Employee ID
            $table->string('company_name')->nullable(); // Company Name
            $table->string('position')->nullable(); // Position
            $table->string('name'); // Name
            $table->integer('age')->nullable(); // Age
            $table->string('resident_registration_number')->nullable(); // Resident Registration Number
            $table->date('date_of_joining')->nullable(); // Date of Joining
            $table->string('contact_number')->nullable(); // Contact Number
            $table->integer('work_days')->nullable(); // Work Days
            
            // Salary and Allowances
            $table->decimal('base_salary', 12, 2)->nullable(); // Base Salary
            $table->decimal('qualification_allowance', 12, 2)->nullable(); // Qualification Allowance
            $table->decimal('position_allowance', 12, 2)->nullable(); // Position Allowance
            $table->decimal('duty_allowance', 12, 2)->nullable(); // Duty Allowance
            $table->decimal('overtime_allowance', 12, 2)->nullable(); // Overtime Allowance
            $table->decimal('holiday_work_allowance', 12, 2)->nullable(); // Holiday Work Allowance
            $table->decimal('night_shift_allowance', 12, 2)->nullable(); // Night Shift Allowance
            $table->decimal('bonus', 12, 2)->nullable(); // Bonus
            $table->decimal('adjustment_allowance', 12, 2)->nullable(); // Adjustment Allowance
            $table->decimal('transportation_allowance', 12, 2)->nullable(); // Transportation Allowance
            $table->decimal('meal_allowance', 12, 2)->nullable(); // Meal Allowance
            $table->decimal('labor_day_allowance', 12, 2)->nullable(); // Labor Day Allowance
            $table->decimal('paid_leave_allowance', 12, 2)->nullable(); // Paid Leave Allowance
            $table->decimal('welfare_allowance', 12, 2)->nullable(); // Welfare Allowance
            $table->decimal('other_allowances', 12, 2)->nullable(); // Other Allowances
            $table->decimal('total_earnings', 12, 2)->nullable(); // Total Earnings
            
            // Deductions
            $table->decimal('health_insurance', 12, 2)->nullable(); // Health Insurance
            $table->decimal('long_term_care_insurance', 12, 2)->nullable(); // Long-term Care Insurance
            $table->decimal('employment_insurance', 12, 2)->nullable(); // Employment Insurance
            $table->decimal('national_pension', 12, 2)->nullable(); // National Pension
            $table->decimal('income_tax', 12, 2)->nullable(); // Income Tax
            $table->decimal('local_income_tax', 12, 2)->nullable(); // Local Income Tax
            $table->decimal('other_deductions', 12, 2)->nullable(); // Other Deductions
            $table->decimal('total_deductions', 12, 2)->nullable(); // Total Deductions
            $table->decimal('net_payment', 12, 2)->nullable(); // Net Payment
            
            $table->text('remarks')->nullable(); // Remarks
            $table->string('import_batch')->nullable(); // Track import batches
            $table->timestamp('imported_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_summaries');
    }
};
