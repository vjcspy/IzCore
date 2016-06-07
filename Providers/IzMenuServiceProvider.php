<?php
/**
 * Created by PhpStorm.
 * User: vjcspy
 * Date: 07/06/2016
 * Time: 12:00
 */

namespace Modules\IzCore\Providers;


use Illuminate\Support\ServiceProvider;

class IzMenuServiceProvider extends ServiceProvider {

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        // TODO: Implement register() method.
        $this->app->singleton(
            'izMenu',
            function ($app) {
                return $app->make('\Modules\IzCore\Repositories\IzMenu');
            });
    }
}