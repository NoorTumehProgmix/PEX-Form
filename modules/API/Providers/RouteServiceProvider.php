<?php


namespace Juzaweb\API\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Juzaweb\Applications\Models\Application;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::bind('application', function ($value) {
            $application = Application::find($value);

            if (is_null($application)) {
                return [
                    'error' => 'Resource not found'
                ];
            }

            return $application;
        });
    }

    public function map()
    {
        $this->mapAdminRoutes();
        $this->mapApiRoutes();
    }

    protected function mapApiRoutes(): void
    {
        Route::prefix('api')
            ->middleware('api')
            ->as('api.')
            ->group(__DIR__ . '/../routes/api.php');
    }

    protected function mapAdminRoutes(): void
    {
        Route::prefix(config('juzaweb.admin_prefix'))
            ->middleware('admin')
            ->group(__DIR__ . '/../routes/admin.php');
    }
}
