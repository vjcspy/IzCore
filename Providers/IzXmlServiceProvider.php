<?php
/**
 * Created by PhpStorm.
 * User: vjcspy
 * Date: 08/06/2016
 * Time: 17:01
 */

namespace Modules\IzCore\Providers;


use Cartalyst\Support\ServiceProvider;

class IzXmlServiceProvider extends ServiceProvider {

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        // TODO: Implement register() method.
        $this->app->singleton(
            'IzXml',
            function ($app) {
                return $app->make('\Modules\IzCore\Repositories\IzXml');
            });
    }
}