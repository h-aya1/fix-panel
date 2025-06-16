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
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->string('uid')->unique();
            $table->string('employee_id')->index();
            $table->string('department');
            $table->string('position');
            $table->string('name');
            $table->string('phone_number')->nullable();
            $table->integer('work_days')->default(0);
            $table->decimal('base_salary', 15, 2)->default(0);
            $table->string('base_salary_str')->nullable();
            $table->decimal('allowances', 15, 2)->default(0);
            $table->string('allowances_str')->nullable();
            $table->decimal('gross_pay', 15, 2)->default(0);
            $table->string('gross_pay_str')->nullable();
            $table->decimal('deductions', 15, 2)->default(0);
            $table->string('deductions_str')->nullable();
            $table->decimal('net_pay', 15, 2)->default(0);
            $table->string('net_pay_str')->nullable();
            $table->text('remarks')->nullable();
            $table->enum('sms_sent_status', ['sent', 'pending', 'failed'])->default('pending');
            $table->json('allowance_items')->nullable();
            $table->json('deduction_items')->nullable();
            $table->json('sms_details')->nullable();
            $table->boolean('is_checked')->default(false);
            $table->decimal('numeric_base_salary', 15, 2)->default(0);
            $table->decimal('numeric_total_allowances', 15, 2)->default(0);
            $table->decimal('numeric_total_deductions', 15, 2)->default(0);
            $table->decimal('numeric_net_pay', 15, 2)->default(0);
            $table->string('payroll_month')->nullable();
            $table->integer('payroll_year')->nullable();
            $table->timestamps();

            // Indexes for better performance
            $table->index(['payroll_month', 'payroll_year']);
            $table->index('sms_sent_status');
            $table->index('department');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
