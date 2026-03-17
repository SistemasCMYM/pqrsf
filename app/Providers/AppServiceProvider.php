<?php

namespace App\Providers;

use App\Models\EstadoCuentaResumen;
use App\Models\HoldingCompany;
use App\Models\Pqrsf;
use App\Policies\EstadoCuentaResumenPolicy;
use App\Policies\PqrsfPolicy;
use App\Services\EstadoCuenta\DataSources\AccountStatementDataSourceInterface;
use App\Services\EstadoCuenta\DataSources\ApiAccountStatementDataSource;
use App\Services\EstadoCuenta\DataSources\ExcelAccountStatementDataSource;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AccountStatementDataSourceInterface::class, function () {
            return config('account_statement.source') === 'api'
                ? new ApiAccountStatementDataSource()
                : new ExcelAccountStatementDataSource();
        });
    }

    public function boot(): void
    {
        Gate::policy(Pqrsf::class, PqrsfPolicy::class);
        Gate::policy(EstadoCuentaResumen::class, EstadoCuentaResumenPolicy::class);

        $activeBrand = null;
        if (Schema::hasTable('holding_companies')) {
            $brandSlug = null;
            if (! app()->runningInConsole()) {
                $request = request();
                if ($request->hasSession()) {
                    $brandSlug = $request->session()->get('brand_slug');
                }
            }

            $query = HoldingCompany::query()->where('active', true);
            if ($brandSlug) {
                $query->where('slug', $brandSlug);
            } else {
                $query->orderByDesc('is_default')->orderBy('name');
            }

            $activeBrand = $query->first();

            if (! $activeBrand) {
                $activeBrand = HoldingCompany::query()->where('active', true)->orderByDesc('is_default')->orderBy('name')->first();
            }
        }

        View::share('activeBrand', $activeBrand);
    }
}
