<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\language\LanguageController;
use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Protected routes - require authentication
    Route::get('/employees', [App\Http\Controllers\EmployeeController::class, 'index'])->name('employees.index');
    Route::get('/payrolls', [App\Http\Controllers\PayrollController::class, 'index'])->name('payrolls.index');
    Route::get('/payrolls/print', [App\Http\Controllers\PayrollController::class, 'print'])->name('payrolls.print');
    
    // Employee routes
    Route::resource('employees', EmployeeController::class)->except(['show']);
    Route::get('employees-import', [EmployeeController::class, 'importForm'])->name('employees.import.form');
    Route::post('employees-import', [EmployeeController::class, 'import'])->name('employees.import');
    Route::post('employees/import', [\App\Http\Controllers\EmployeeController::class, 'import'])->name('employees.import');
    
    // Payroll routes
    Route::get('/payrolls/employees', [App\Http\Controllers\PayrollController::class, 'getEmployees'])->name('payrolls.employees');
    Route::get('/payrolls/employees/{employeeId}', [App\Http\Controllers\PayrollController::class, 'getEmployee'])->name('payrolls.employee');
    Route::post('/payrolls', [App\Http\Controllers\PayrollController::class, 'store'])->name('payrolls.store');
    Route::put('/payrolls/{id}', [App\Http\Controllers\PayrollController::class, 'update'])->name('payrolls.update');
    Route::delete('/payrolls/{id}', [App\Http\Controllers\PayrollController::class, 'destroy'])->name('payrolls.destroy');
    Route::delete('/payrolls/bulk-delete', [App\Http\Controllers\PayrollController::class, 'bulkDelete'])->name('payrolls.bulk-delete');
    Route::post('/payrolls/import', [App\Http\Controllers\PayrollController::class, 'import'])->name('payrolls.import');
    Route::post('/payrolls/update-sms-status', [App\Http\Controllers\PayrollController::class, 'updateSmsStatus'])->name('payrolls.update-sms-status');
});

// Language routes (accessible to all users)
Route::get('lang/{locale}', [LanguageController::class, 'swap'])->name('lang.swap');

// Test route for payroll functionality (remove in production)
Route::get('/test-payroll', function() {
    return view('test-payroll');
})->name('test.payroll');

Route::get('/test-modal', function() {
    return view('test-modal');
})->name('test.modal');

require __DIR__.'/auth.php';
