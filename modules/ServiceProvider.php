<?php

namespace Modules;

use Illuminate\Support\ServiceProvider as ModuleServiceProvider;
use Illuminate\Support\Facades\File;

class ServiceProvider extends ModuleServiceProvider
{
    /*
    |--------------------------------------------------------------------------
    | Module Service Provider
    |--------------------------------------------------------------------------
    |
    | This service use register and load all source on modules.
    |
    */

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // get list name Module
        $listModule = array_map('basename', File::directories(__DIR__));

        foreach ($listModule as $module) {
            // variable
            $viewPath = __DIR__ . "/$module/resources/views";
            $routePathFile = __DIR__ . "/$module/routes/api.php";
            $migrationPath  = __DIR__ . "/$module/database/migrations";
            $translationPath = __DIR__ . "/$module/resources/lang";

            // load view
            if (is_dir($viewPath)) {
                $this->loadViewsFrom($viewPath, $module);
            }

            // load migration
            if (is_dir($migrationPath)) {
                $this->loadMigrationsFrom($migrationPath);
            }

            // load translation
            if (is_dir($translationPath)) {
                $this->loadTranslationsFrom($translationPath, $module);
            }

            // load routes web
            if (file_exists($routePathFile)) {
                $this->loadRoutesFrom($routePathFile);
            }
        }
    }
}
