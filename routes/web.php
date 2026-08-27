<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\PayrollTemplateController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use App\Models\Payroll;
use App\Jobs\SendPayrollSlipEmailJob;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin/payrolls/stop-queue', function () {

    // 1. Hentikan worker secara graceful
    Artisan::call('queue:restart');

    // 2. Hapus semua pending job dari database queue
    $deletedJobs = DB::table('jobs')
        ->where('queue', 'default')
        ->delete();

    // 3. Clear failed jobs juga kalau memang ingin reset total
    // Jangan aktifkan kalau failed_jobs masih ingin disimpan untuk debugging.
    // DB::table('failed_jobs')->delete();

    // 4. Clear Laravel cache
    Artisan::call('cache:clear');
    $cacheOutput = Artisan::output();

    // 5. Clear config
    Artisan::call('config:clear');
    $configOutput = Artisan::output();

    // 6. Clear view
    Artisan::call('view:clear');
    $viewOutput = Artisan::output();

    return response()->json([
        'success' => true,
        'message' => 'Queue payroll berhasil dihentikan/reset.',
        'deleted_jobs' => $deletedJobs,
        'cache_clear' => $cacheOutput,
        'config_clear' => $configOutput,
        'view_clear' => $viewOutput,
    ]);
});

Route::get('/resend-payroll-queue', function () {

    $total = 0;

    Payroll::where('period', '21 April 2026 - 20 Mei 2026')
        ->whereIn('email_status', ['pending', 'failed'])
        ->chunk(50, function ($payrolls) use (&$total) {

            foreach ($payrolls as $payroll) {

                $payroll->update([
                    'email_status' => 'queued',
                    'email_error' => null,
                ]);

                SendPayrollSlipEmailJob::dispatch($payroll);

                $total++;
            }
        });

    return "Total queue dispatched: {$total}";
});

Route::get('/run-worker', function () {

    try {

        Artisan::call('queue:work', [
            '--queue' => 'default',
            '--once' => true,
            '--tries' => 3,
            '--timeout' => 120,
        ]);

        return '<pre>' . Artisan::output() . '</pre>';
    } catch (\Throwable $e) {

        return response()->json([
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
    }
});

Route::get('/clear-config', function () {
    Artisan::call('optimize:clear');

    return 'Config cleared';
});

Route::get('/debug-server', function () {
    return response()->json([
        'php' => PHP_VERSION,
        'env' => app()->environment(),
        'debug' => config('app.debug'),
        'view_welcome' => view()->exists('welcome'),
        'manifest' => file_exists(public_path('build/manifest.json')),
        'storage_writable' => is_writable(storage_path()),
        'cache_writable' => is_writable(base_path('bootstrap/cache')),
    ]);
});


Route::get('/test-mail', function () {

    Mail::raw('Test email dari payroll system', function ($message) {

        $message->to('pujiermanto@gmail.com')
            ->subject('Test Email Resend');
    });

    return 'Email berhasil dikirim';
});

Route::get('/deploy-migrate-20260802/{key}', function ($key) {
    abort_unless(hash_equals($key, 'pyrl-systemxx-migrate-8x29-private'), 404);

    Artisan::call('migrate', [
        '--force' => true,
    ]);

    $migrateOutput = Artisan::output();

    Artisan::call('db:seed', [
        '--class' => 'AdminCompanySeeder',
        '--force' => true,
    ]);

    $seedOutput = Artisan::output();

    Artisan::call('optimize:clear');

    return response(
        '<pre>MIGRATE:' . PHP_EOL . e($migrateOutput) .
            PHP_EOL . PHP_EOL . 'SEED:' . PHP_EOL . e($seedOutput) . '</pre>'
    );
});

Route::get('/cleanup-employee-users-20260802/{key}', function ($key) {
    abort_unless(hash_equals($key, 'pyrl-cleanup-users-8x29-private'), 404);

    Artisan::call('db:seed', [
        '--class' => 'CleanupEmployeeUsersSeeder',
        '--force' => true,
    ]);

    Artisan::call('optimize:clear');

    return response('<pre>' . e(Artisan::output()) . '</pre>');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {

        // Route::get('payrolls/queue-status', function () {

        //     $pending = DB::table('jobs')
        //         ->where('queue', 'default')
        //         ->whereNull('reserved_at')
        //         ->count();

        //     return response()->json([
        //         'processing' => $pending > 0,
        //         'pending_jobs' => $pending,
        //     ]);
        // })->name('payrolls.queue-status');
        Route::get('payrolls/queue-status', function () {

            $pending = DB::table('jobs')
                ->where('queue', 'default')
                ->count();

            return response()->json([
                'processing' => $pending > 0,
                'pending_jobs' => $pending,
            ]);
        })->name('payrolls.queue-status');

        // CUSTOM ROUTES DULU
        Route::get('payrolls/import', [PayrollController::class, 'import'])
            ->name('payrolls.import');

        Route::post('payrolls/import', [PayrollController::class, 'importStore'])
            ->name('payrolls.import.store');

        Route::post('payrolls/bulk-email', [PayrollController::class, 'sendBulk'])
            ->name('payrolls.bulk-email');

        Route::get('payrolls/export', [PayrollController::class, 'export'])
            ->name('payrolls.export');

        Route::get('payrolls/{payroll}/download', [PayrollController::class, 'download'])
            ->name('payrolls.download');

        Route::get('payrolls/{payroll}/preview', [PayrollController::class, 'preview'])
            ->name('payrolls.preview');

        Route::delete('payrolls/destroy-all', [PayrollController::class, 'destroyAll'])
            ->name('payrolls.destroy-all');

        Route::get('payroll-templates/default/preview', [PayrollTemplateController::class, 'previewDefault'])
            ->name('payroll-templates.default.preview');

        Route::post('payroll-templates/{payrollTemplate}/activate', [PayrollTemplateController::class, 'activate'])
            ->name('payroll-templates.activate');

        Route::post('payroll-templates/deactivate', [PayrollTemplateController::class, 'deactivate'])
            ->name('payroll-templates.deactivate');

        Route::get('payroll-templates/{payrollTemplate}/download', [PayrollTemplateController::class, 'download'])
            ->name('payroll-templates.download');

        Route::resource('payroll-templates', PayrollTemplateController::class)
            ->parameters(['payroll-templates' => 'payrollTemplate'])
            ->except('show');

        // RESOURCE PALING BAWAH
        Route::resource('payrolls', PayrollController::class);

        // OTHER
        Route::resource('departments', DepartmentController::class);
        Route::resource('positions', PositionController::class);

        Route::get('employees/import', [EmployeeController::class, 'import'])
            ->name('employees.import');

        Route::post('employees/import', [EmployeeController::class, 'importStore'])
            ->name('employees.import.store');

        Route::get('employees/export', [EmployeeController::class, 'export'])
            ->name('employees.export');

        Route::delete('employees/bulk-delete', [EmployeeController::class, 'bulkDestroy'])
            ->name('employees.bulk-delete');

        Route::delete('employees/destroy-all', [EmployeeController::class, 'destroyAll'])
            ->name('employees.destroy-all');

        Route::resource('employees', EmployeeController::class);
    });

    Route::middleware(['role:employee'])->prefix('employee')->name('employee.')->group(function () {
        Route::get('payrolls', [PayrollController::class, 'employeeIndex'])->name('payrolls.index');
        Route::get('payrolls/{payroll}', [PayrollController::class, 'employeeShow'])->name('payrolls.show');
        Route::get('payrolls/{payroll}/download', [PayrollController::class, 'download'])->name('payrolls.download');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


require __DIR__ . '/auth.php';
