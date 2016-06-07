<?php
/**
 * Manage all setting in  admin app
 * User: vjcspy
 * Date: 06/06/2016
 * Time: 10:05
 */

namespace Modules\IzCore\Providers;


use Illuminate\Support\ServiceProvider;

class IzConfigServiceProvider extends ServiceProvider {

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        // TODO: Implement register() method.
        $this->app->singleton(
            'coreConfig',
            function ($app) {
                return $app->make('Modules\IzCore\Repositories\CoreConfig');
            });
    }
}