<?php
/**
 * Created by PhpStorm.
 * User: vjcspy
 * Date: 26/05/2016
 * Time: 17:36
 */

namespace Modules\IzCore\Http\Controllers;


use Theme;

class DemoController extends ThemeAbstractController {

    public function getIndex() {
        $this->setTheme('admin.material');
        $view = [
            'name' => 'Teepluss'
        ];


        // home.index will look up the path 'public/themes/default/views/home/index.php'
        return $this->theme->scope('home.index', $view)->render();
    }
}