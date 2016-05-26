<?php namespace Modules\IzCore\Providers;

use Illuminate\Support\ServiceProvider;
use Artisan;

class IzCoreServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    protected $commands
        = [
            'Modules\IzCore\Console\PublishConfigCommand'
        ];

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot() {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        $this->registerDependencyLibrary();

        /*Register command izCore*/
        $this->commands($this->commands);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig() {
        $this->publishes(
            [
                __DIR__ . '/../Config/config.php' => config_path('izcore.php'),
            ]);
        $this->mergeConfigFrom(
            __DIR__ . '/../Config/config.php',
            'izcore'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews() {
        $viewPath = base_path('resources/views/modules/izcore');

        $sourcePath = __DIR__ . '/../Resources/views';

        $this->publishes(
            [
                $sourcePath => $viewPath
            ]);

        $this->loadViewsFrom(
            array_merge(
                array_map(
                    function ($path) {
                        return $path . '/modules/izcore';
                    },
                    \Config::get('view.paths')),
                [$sourcePath]),
            'izcore');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations() {
        $langPath = base_path('resources/lang/modules/izcore');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'izcore');
        }
        else {
            $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'izcore');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides() {
        return [];
    }

    public function registerDependencyLibrary() {
        $this->app->register('\Teepluss\Theme\ThemeServiceProvider');
        $this->app->register('\Pingpong\Menus\MenusServiceProvider');
        $this->app->register('\Intervention\Image\ImageServiceProvider');
    }
}
