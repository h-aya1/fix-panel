<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\language\LanguageController;
use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Route;
use PhpOffice\PhpSpreadsheet\IOFactory;

Route::get('/', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('home');

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
    
    // Employee routes - define specific routes before resource to avoid conflicts
    
    // Bulk delete route (must be defined before resource)
    Route::post('employees/bulk-delete', [EmployeeController::class, 'bulkDelete'])
        ->name('employees.bulk-delete');
        
    // Truncate all employees route
    Route::post('employees/truncate-all', [EmployeeController::class, 'truncateAll'])
        ->name('employees.truncate-all');
        
    // Main resource route for employees (with show excluded)
    Route::resource('employees', EmployeeController::class)->except(['show']);
    
    // Additional employee routes under prefix
    Route::prefix('employees')->name('employees.')->group(function () {
        Route::get('import', [EmployeeController::class, 'importForm'])->name('import.form');
        Route::post('import', [EmployeeController::class, 'import'])->name('import');
        Route::post('preview', [EmployeeController::class, 'preview'])->name('preview');
        Route::post('save-preview', [EmployeeController::class, 'savePreview'])->name('save-preview');
        Route::get('template/download', [EmployeeController::class, 'downloadTemplate'])->name('template.download');
    });
    
    // Payroll routes
    Route::get('/payrolls/employees', [App\Http\Controllers\PayrollController::class, 'getEmployees'])->name('payrolls.employees');
    Route::get('/payrolls/employees/{employeeId}', [App\Http\Controllers\PayrollController::class, 'getEmployee'])->name('payrolls.employee');
    Route::post('/payrolls', [App\Http\Controllers\PayrollController::class, 'store'])->name('payrolls.store');
    Route::put('/payrolls/{id}', [App\Http\Controllers\PayrollController::class, 'update'])->name('payrolls.update');
    Route::delete('/payrolls/{id}', [App\Http\Controllers\PayrollController::class, 'destroy'])->name('payrolls.destroy');
    Route::delete('/payrolls/bulk-delete', [App\Http\Controllers\PayrollController::class, 'bulkDelete'])->name('payrolls.bulk-delete');
    Route::post('/payrolls/import', [App\Http\Controllers\PayrollController::class, 'import'])->name('payrolls.import');
    Route::post('/payrolls/update-sms-status', [App\Http\Controllers\PayrollController::class, 'updateSmsStatus'])->name('payrolls.update-sms-status');
    
    // Employee Summary routes
    Route::get('/employee-summaries', [App\Http\Controllers\EmployeeSummaryController::class, 'index'])->name('employee-summaries.index');
    Route::get('/employee-summaries/import', [App\Http\Controllers\EmployeeSummaryController::class, 'importForm'])->name('employee-summaries.import.form');
    Route::post('/employee-summaries/import', [App\Http\Controllers\EmployeeSummaryController::class, 'import'])->name('employee-summaries.import');
    Route::post('/employee-summaries/preview', [App\Http\Controllers\EmployeeSummaryController::class, 'preview'])->name('employee-summaries.preview');
    Route::post('/employee-summaries/save-preview', [App\Http\Controllers\EmployeeSummaryController::class, 'savePreview'])->name('employee-summaries.save-preview');
    Route::delete('/employee-summaries/{id}', [App\Http\Controllers\EmployeeSummaryController::class, 'destroy'])->name('employee-summaries.destroy');
    Route::delete('/employee-summaries/delete-all', [App\Http\Controllers\EmployeeSummaryController::class, 'deleteAll'])->name('employee-summaries.delete-all');
    Route::get('/employee-summaries/stats', [App\Http\Controllers\EmployeeSummaryController::class, 'getImportStats'])->name('employee-summaries.stats');
    Route::resource('employee-summaries', App\Http\Controllers\EmployeeSummaryController::class)->except(['index', 'destroy']);
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

Route::get('/test-import', function () {
    return view('employees.import');
});

// Test route to debug Excel import
Route::post('/test-upload', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
    ]);

    $file = $request->file('file');
    
    try {
        // Log file info
        \Log::info('Test upload received', [
            'name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'mime' => $file->getMimeType(),
            'extension' => $file->getClientOriginalExtension()
        ]);

        // Read the Excel file
        $spreadsheet = IOFactory::load($file->getPathname());
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();
        
        // Log the first 5 rows for debugging
        $sampleRows = array_slice($rows, 0, 5);
        \Log::debug('First 5 rows of Excel file', ['rows' => $sampleRows]);
        
        // Return the first 5 rows as JSON
        return response()->json([
            'success' => true,
            'headers' => !empty($rows[0]) ? $rows[0] : [],
            'first_few_rows' => $sampleRows,
            'total_rows' => count($rows),
            'file_info' => [
                'name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime' => $file->getMimeType(),
                'extension' => $file->getClientOriginalExtension()
            ]
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Error reading Excel file', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
})->name('test.upload');

require __DIR__.'/auth.php';