<?php

use App\Http\Controllers\EstadoCuenta\ApiSyncController;
use App\Http\Controllers\EstadoCuenta\EstadoCuentaAdminController;
use App\Http\Controllers\EstadoCuenta\EstadoCuentaDashboardController;
use App\Http\Controllers\EstadoCuenta\ImportacionExcelController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Pqrsf\PqrsfDashboardController;
use App\Http\Controllers\Pqrsf\PqrsfManagementController;
use App\Http\Controllers\PublicPqrsfController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::get('/pqrsf/registro', [PublicPqrsfController::class, 'create'])->name('public.pqrsf.create');
Route::post('/pqrsf/registro', [PublicPqrsfController::class, 'store'])->middleware('throttle:8,1')->name('public.pqrsf.store');
Route::get('/pqrsf/registro/exito/{radicado}', [PublicPqrsfController::class, 'success'])->name('public.pqrsf.success');
Route::get('/empresa/{slug}/login', function (string $slug) {
    if (\Illuminate\Support\Facades\Schema::hasTable('holding_companies')) {
        $company = \App\Models\HoldingCompany::query()->where('slug', $slug)->where('active', true)->first();
        if ($company) {
            session(['brand_slug' => $slug]);
        }
    }

    return redirect()->route('login');
})->name('brand.login');

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::view('/dashboard', 'dashboard')->name('dashboard');

    Route::get('/pqrsf/dashboard', PqrsfDashboardController::class)
        ->middleware('ensure.role:Administrador,Admin PQRSF,Gestor PQRSF')
        ->name('pqrsf.dashboard');

    Route::get('/pqrsf', [PqrsfManagementController::class, 'index'])
        ->middleware('ensure.role:Administrador,Admin PQRSF,Gestor PQRSF,Asesor')
        ->name('pqrsf.index');
    Route::get('/pqrsf/configuracion', [\App\Http\Controllers\Pqrsf\PqrsfConfigController::class, 'index'])
        ->middleware('ensure.role:Administrador,Admin PQRSF')
        ->name('pqrsf.config.index');
    Route::post('/pqrsf/configuracion/sla', [\App\Http\Controllers\Pqrsf\PqrsfConfigController::class, 'updateSla'])
        ->middleware('ensure.role:Administrador,Admin PQRSF')
        ->name('pqrsf.config.sla');
    Route::post('/pqrsf/configuracion/responsables', [\App\Http\Controllers\Pqrsf\PqrsfConfigController::class, 'updateResponsables'])
        ->middleware('ensure.role:Administrador,Admin PQRSF')
        ->name('pqrsf.config.responsables');
    Route::post('/pqrsf/configuracion/destinatarios', [\App\Http\Controllers\Pqrsf\PqrsfConfigController::class, 'storeDestinatario'])
        ->middleware('ensure.role:Administrador,Admin PQRSF')
        ->name('pqrsf.config.destinatarios.store');
    Route::patch('/pqrsf/configuracion/destinatarios/{destinatario}', [\App\Http\Controllers\Pqrsf\PqrsfConfigController::class, 'updateDestinatario'])
        ->middleware('ensure.role:Administrador,Admin PQRSF')
        ->name('pqrsf.config.destinatarios.update');
    Route::get('/pqrsf/{pqrsf}', [PqrsfManagementController::class, 'show'])
        ->middleware('ensure.role:Administrador,Admin PQRSF,Gestor PQRSF,Asesor')
        ->name('pqrsf.show');
    Route::patch('/pqrsf/{pqrsf}', [PqrsfManagementController::class, 'update'])
        ->middleware('ensure.role:Administrador,Admin PQRSF,Gestor PQRSF')
        ->name('pqrsf.update');
    Route::post('/pqrsf/{pqrsf}/assign', [PqrsfManagementController::class, 'assign'])
        ->middleware('ensure.role:Administrador,Admin PQRSF,Gestor PQRSF')
        ->name('pqrsf.assign');
    Route::post('/pqrsf/{pqrsf}/responses', [PqrsfManagementController::class, 'addResponse'])
        ->middleware('ensure.role:Administrador,Admin PQRSF,Gestor PQRSF')
        ->name('pqrsf.responses.store');

    Route::get('/estado-cuenta', EstadoCuentaDashboardController::class)
        ->middleware('ensure.role:Administrador,Coordinador Estado Cuenta,Asesor')
        ->name('estado-cuenta.dashboard');

    Route::middleware('ensure.role:Administrador,Coordinador Estado Cuenta')->prefix('estado-cuenta/admin')->name('estado-cuenta.admin.')->group(function (): void {
        Route::get('/', [EstadoCuentaAdminController::class, 'index'])->name('index');
        Route::post('/importar', [ImportacionExcelController::class, 'store'])->name('import');
        Route::post('/config', [EstadoCuentaAdminController::class, 'updateConfig'])->name('config.update');
        Route::post('/sync-api', ApiSyncController::class)->name('sync-api');
    });

    Route::middleware('ensure.role:Administrador')->prefix('admin')->name('admin.')->group(function (): void {
        Route::get('/usuarios', [\App\Http\Controllers\Admin\UserManagementController::class, 'index'])->name('users.index');
        Route::get('/usuarios/plantilla', [\App\Http\Controllers\Admin\UserManagementController::class, 'downloadTemplate'])->name('users.template');
        Route::post('/usuarios', [\App\Http\Controllers\Admin\UserManagementController::class, 'store'])->name('users.store');
        Route::post('/usuarios/carga-masiva', [\App\Http\Controllers\Admin\UserManagementController::class, 'bulkStore'])->name('users.bulk');
        Route::patch('/usuarios/{user}', [\App\Http\Controllers\Admin\UserManagementController::class, 'updateRoles'])->name('users.update');
        Route::get('/holding/empresas', [\App\Http\Controllers\Admin\HoldingCompanyController::class, 'index'])->name('holding.companies.index');
        Route::post('/holding/empresas', [\App\Http\Controllers\Admin\HoldingCompanyController::class, 'store'])->name('holding.companies.store');
        Route::patch('/holding/empresas/{company}', [\App\Http\Controllers\Admin\HoldingCompanyController::class, 'update'])->name('holding.companies.update');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
