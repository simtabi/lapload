<?php

namespace Simtabi\Lapload\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Simtabi\Lapload\Http\Livewire\Uploader;
use Simtabi\Lapload\Helpers\LaploadHelper;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Compilers\BladeCompiler;

class LaploadServiceProvider extends ServiceProvider
{

    private string $packageName = 'lapload';
    private const  PACKAGE_PATH = __DIR__ . '/../../';

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
     * Register the application services.
     */
    public function register()
    {
        $this->loadTranslationsFrom(self::PACKAGE_PATH . "resources/lang/", $this->packageName);
        $this->loadMigrationsFrom(self::PACKAGE_PATH.'/../database/migrations');
        $this->loadViewsFrom(self::PACKAGE_PATH . "resources/views", $this->packageName);
        $this->mergeConfigFrom(self::PACKAGE_PATH . "config/{$this->packageName}.php", $this->packageName);
    }

    /**
     * Bootstrap the application services.
     */
    public function boot()
    {

        $this->registerConsoles();
        $this->registerDirectives();
        $this->configureComponents();

        Livewire::component(LaploadHelper::getPackageName(), Uploader::class);
    }

    private function registerConsoles(bool $publishPublicAssets = false): static
    {

        if ($this->app->runningInConsole())
        {

            $this->publishes([
                self::PACKAGE_PATH . "config/{$this->packageName}.php" => config_path("{$this->packageName}.php"),
            ], "{$this->packageName}:config");

            $this->publishes([
                self::PACKAGE_PATH . "public"                          => public_path("vendor/{$this->packageName}"),
            ], "{$this->packageName}:assets");

            $this->publishes([
                self::PACKAGE_PATH . "resources/views"                 => resource_path("views/vendor/{$this->packageName}"),
            ], "{$this->packageName}:views");

            $this->publishes([
                self::PACKAGE_PATH . "resources/lang"                  => $this->app->langPath("vendor/{$this->packageName}"),
            ], "{$this->packageName}:translations");
        }

        return $this;
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
                return asset("/vendor/".LaploadHelper::getPackageName()."/css/{$item}");
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
                return asset("/vendor/".LaploadHelper::getPackageName()."/js/{$item}");
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


    protected function configureComponents()
    {
        $this->callAfterResolving(BladeCompiler::class, function () {
            $this->registerComponent('uploader');
        });
    }

    /**
     * Register the given component.
     *
     * @param  string  $component
     * @return void
     */
    protected function registerComponent(string $component, $alias = LaploadHelper::PACKAGE_NAME)
    {
        Blade::component(LaploadHelper::getPackageName().'::components.'.$component, (!empty($alias) ? "$alias-" : '').$component);
    }

}
