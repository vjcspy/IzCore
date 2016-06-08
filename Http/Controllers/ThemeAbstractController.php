<?php
/**
 * Created by PhpStorm.
 * User: vjcspy
 * Date: 26/05/2016
 * Time: 18:34
 */

namespace Modules\IzCore\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\IzCore\Repositories\IzXml;
use Modules\IzCore\Repositories\Theme as izTheme;
use Modules\IzCore\Repositories\Theme\Asset;
use Modules\IzCore\Repositories\Theme\View;
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
     * @var \Illuminate\Http\Request
     */
    protected $request;
    /**
     * @var \Modules\IzCore\Repositories\Theme\Asset
     */
    protected $izAsset;
    /**
     * @var \Modules\IzCore\Repositories\Theme\View
     */
    protected $izView;
    /**
     * @var $_viewData []
     */
    protected $_viewData = [];
    /**
     * @var \Modules\IzCore\Repositories\Theme
     */
    protected $izTheme;
    /**
     * @var \Modules\IzCore\Repositories\IzXml
     */
    protected $izXml;

    /**
     * @param \Teepluss\Theme\Contracts\Theme    $theme
     * @param \Illuminate\Http\Request           $request
     * @param \Modules\IzCore\Repositories\Theme $izTheme
     * @param \Modules\IzCore\Repositories\IzXml $izXml
     */
    public function __construct(
        Theme $theme,
        Request $request,
        izTheme $izTheme,
        IzXml $izXml
    ) {
        $this->theme   = $theme;
        $this->request = $request;
        $this->izAsset = app()['izAsset'];
        $this->izView  = app()['izView'];
        $this->izTheme = $izTheme;
        $this->izXml   = $izXml;
    }

    /**
     * @param $layout
     *
     * @return $this
     */
    public function setLayout($layout) {
        $this->theme->layout($layout);

        return $this;
    }

    /**
     * @param $theme
     *
     * @return $this
     */
    public function setTheme($theme) {
        $this->theme->uses($theme);

        return $this;
    }

    /**
     * Inject view and asset from another modules
     *
     * @return \Illuminate\Http\Response
     * @throws \Exception
     *
     */
    public function renderHtml() {
        $path = str_replace("/", "_", $this->request->path());

        // get addition assets from another modules
        $this->izAsset->setTheme($this->theme)->setCurrentPath($path)->initAssets($this->theme);

        // merger view data from another modules
        $this->izView->setLayout($this->theme->getLayoutName())->initAdditionViews($this->_viewData, $path);

        // merge theme data from another modules
        $this->izTheme->initViewData($this->theme, $path);

        // get view name
        $viewFile = debug_backtrace()[1]['function'];

        return $this->theme->scopeWithLayout(str_replace('/', '.', $this->request->path()) . '.' . $viewFile, $this->_viewData)->render();
    }

    /**
     * Wrap function addAssets
     *
     * @param $assets
     *
     * @return $this
     */
    protected function addAssets($assets, $path = null) {
        if (is_null($path))
            $path = $this->request->path();
        $this->izAsset->addAssets($path, $assets);

        return $this;
    }

    /**
     * @param $viewData
     */
    protected function setViewData($viewData) {
        $this->_viewData = $viewData;
    }

    /**
     * Add data to theme
     *
     * @param $data
     *
     * @return $this
     */
    protected function setThemeData($data) {
        foreach ($data as $k => $item) {
            $this->theme->set($k, $item);
        }

        return $this;
    }

    /**
     * Wrap functin addCustomAssets from izAsset
     * For add assets not in bower_components
     *
     * @param $customAssets
     *
     * @return $this
     * @throws \Exception
     */
    protected function addCustomAssets($customAssets) {
        $this->izAsset->addCustomAssets($this->request->path(), $customAssets);

        return $this;
    }

}