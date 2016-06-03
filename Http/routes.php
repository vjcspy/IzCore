<?php

Route::group(
    ['middleware' => 'web', 'prefix' => 'izcore', 'namespace' => 'Modules\IzCore\Http\Controllers'],
    function () {
        Route::get('/', 'IzCoreController@index');
    });