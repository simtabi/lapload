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

    public static array $cdnAssets = [
        'css'  => [
        ],
        'js' => [
        ],
    ];

    public static array $assets    = [
        'css'  => [
            'lapload.css',
        ],
        'js' => [

        ],
    ];

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

        Blade::directive(LaploadHelper::getPackageName().'Css', function () {
            $styles  = $this->getComponentCdnStyles();
            $styles .= $this->getComponentStyles();
            return $styles;
        });

        Blade::directive(LaploadHelper::getPackageName().'Js', function () {
            $scripts  = $this->getComponentCdnScripts();
            $scripts .= $this->getComponentScripts();
            return $scripts;
        });
    }

    private function getComponentStyles()
    {
        $styles = self::$assets['css'] ?? [];

        if (is_array($styles) && (count($styles) >= 1)) {

            return collect($styles)->map(function($item) {
                return asset("/vendor/larabell/css/{$item}");
            })->flatten()->map(function($styleUrl) {
                return '<link media="all" type="text/css" rel="stylesheet" href="' . $styleUrl . '">';
            })->implode(PHP_EOL);
        }

        return false;
    }

    private function getComponentScripts()
    {
        $scripts = self::$assets['js'] ?? [];

        if (is_array($scripts) && (count($scripts) >= 1)) {
            return collect($scripts)->map(function($item) {
                return asset("/vendor/larabell/js/{$item}");
            })->flatten()->map(function($scriptUrl) {
                return !empty($scriptUrl) ? '<script src="' . $scriptUrl . '"></script>' : '';
            })->implode(PHP_EOL);
        }

        return false;
    }

    private function getComponentCdnStyles()
    {
        $styles = self::$cdnAssets['css'] ?? [];

        if (is_array($styles) && (count($styles) >= 1)) {

            return collect($styles)->map(function($item) {
                return $item;
            })->flatten()->map(function($styleUrl) {
                return !empty($styleUrl) ? '<link media="all" type="text/css" rel="stylesheet" href="' . $styleUrl . '">' : '';
            })->implode(PHP_EOL);
        }

        return false;
    }

    private function getComponentCdnScripts()
    {

        $scripts = self::$cdnAssets['js'] ?? [];

        if (is_array($scripts) && (count($scripts) >= 1)) {
            return collect($scripts)->map(function($item) {
                return $item;
            })->flatten()->map(function($scriptUrl) {
                return !empty($scriptUrl) ? '<script src="' . $scriptUrl . '"></script>' : '';
            })->implode(PHP_EOL);
        }

        return false;
    }

}
