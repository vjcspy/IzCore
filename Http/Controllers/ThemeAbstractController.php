<?php
/**
 * Created by PhpStorm.
 * User: vjcspy
 * Date: 26/05/2016
 * Time: 18:34
 */

namespace Modules\IzCore\Http\Controllers;

use Illuminate\Routing\Controller;
use Teepluss\Theme\Contracts\Theme;

/**
 * Class ThemeAbstractController
 *
 * @package Modules\IzCore\Http\Controllers
 */
abstract class ThemeAbstractController extends Controller {

    /**
     * @var $theme \Teepluss\Theme\Theme
     */
    protected $theme;

    /**
     * ThemeAbstractController constructor.
     *
     * @param \Teepluss\Theme\Contracts\Theme $theme
     */
    public function __construct(Theme $theme) {
        $this->theme = $theme->uses('default')->layout('default');
    }

    public function setLayout($layout) {
        $this->theme->layout($layout);
    }

    public function setTheme($theme) {
        $this->theme->uses($theme);
    }
}