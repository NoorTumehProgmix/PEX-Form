<?php


namespace Juzaweb\DevTool\Providers;

use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Juzaweb\CMS\Support\ServiceProvider;
use Juzaweb\CMS\Support\Stub;

class DevToolServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->environment('local')) {

            Builder::macro(
                'toRawSql',
                function () {
                    return array_reduce(
                        $this->getBindings(),
                        function ($sql, $binding) {
                            return preg_replace(
                                '/\?/',
                                is_numeric($binding) ? $binding : "'".$binding."'",
                                $sql,
                                1
                            );
                        },
                        $this->toSql()
                    );
                }
            );

            EloquentBuilder::macro(
                'toRawSql',
                function () {
                    return array_reduce(
                        $this->getBindings(),
                        function ($sql, $binding) {
                            return preg_replace(
                                '/\?/',
                                is_numeric($binding) ? $binding : "'".$binding."'",
                                $sql,
                                1
                            );
                        },
                        $this->toSql()
                    );
                }
            );
        }
    }

    public function register()
    {
        $this->setupStubPath();

        $this->app->register(ConsoleServiceProvider::class);
    }

    /**
     * Setup stub path.
     */
    public function setupStubPath(): void
    {
        Stub::setBasePath(__DIR__ . '/../stubs/plugin');
    }
}
