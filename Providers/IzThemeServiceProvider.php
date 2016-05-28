<?php
/**
 * Created by PhpStorm.
 * User: vjcspy
 * Date: 27/05/2016
 * Time: 18:09
 */

namespace Modules\IzCore\Providers;


use Illuminate\Support\ServiceProvider;

class IzThemeServiceProvider extends ServiceProvider {

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        // TODO: Implement register() method.
        $this->app->singleton(
            'izAsset',
            function ($app) {
                return $app->make('Modules\IzCore\Repositories\Theme\Asset');
            });
        $this->app->singleton(
            'izView',
            function ($app) {
                return $app->make('Modules\IzCore\Repositories\Theme\View');
            });
    }
}