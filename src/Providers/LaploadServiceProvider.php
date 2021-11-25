<?php

namespace Simtabi\Lapload\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Simtabi\Lapload\Http\Livewire\Uploader;
use Simtabi\Lapload\Helpers\LaploadHelper;

class LaploadServiceProvider extends ServiceProvider
{

    private const PACKAGE_PATH = __DIR__ . '/../../';

    /**
     * Bootstrap the application services.
     */
    public function boot()
    {

        // merge configurations
        $this->mergeConfigFrom(self::PACKAGE_PATH .'config/config.php', LaploadHelper::getPackageName());

        // load views
        $this->loadViewsFrom(self::PACKAGE_PATH . 'resources/views', LaploadHelper::getPackageName());

        if ( $this->app->runningInConsole()) {
            $this->registerPublishables();
        }

        $this->registerDirectives();

        Livewire::component(LaploadHelper::getPackageName(), Uploader::class);
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        //
    }

    private function registerPublishables(): void
    {
        $this->publishes([
            self::PACKAGE_PATH . 'config/config.php' => config_path(LaploadHelper::getPackageName().'.php'),
        ], LaploadHelper::getPackageName().':config');

        $this->publishes([
            self::PACKAGE_PATH . 'public'            => public_path('vendor/'.LaploadHelper::getPackageName()),
        ], LaploadHelper::getPackageName().':assets');

        $this->publishes([
            self::PACKAGE_PATH . 'resources/views'   => resource_path('views/vendor/'.LaploadHelper::getPackageName()),
        ], LaploadHelper::getPackageName().':views');
    }

    private function registerDirectives()
    {
        // inject required view files
        Blade::include(LaploadHelper::getPackageName().'::scripts', LaploadHelper::getPackageName().'Scripts');
        Blade::include(LaploadHelper::getPackageName().'::styles', LaploadHelper::getPackageName().'Styles');
    }

}
