<?php

namespace tuanlq11;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class AuditingProvider extends ServiceProvider
{

    /**
     *
     */
    public function boot()
    {
        $this->initMigration($this->app);
    }

    /**
     *
     */
    public function register()
    {
        // TODO: Implement register() method.
    }

    /**
     * Copy migration to resources
     *
     * @param Application $app
     */
    private function initMigration(Application $app)
    {
        if ($app instanceof \Illuminate\Foundation\Application && $app->runningInConsole()) {
            $migrationPath = realpath(__DIR__ . '/../database/migrations');
            $this->publishes([$migrationPath => database_path('migrations')]);
        }
    }

}