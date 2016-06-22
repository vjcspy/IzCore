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
use Modules\IzCore\Repositories\Theme as izTheme;
use Modules\IzCore\Repositories\Theme\Asset;
use Modules\IzCore\Repositories\Theme\View;

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
     * Path view
     * @var string
     */
    protected $_viewFile;

    /**
     * @param \Illuminate\Http\Request $request
     *
     */
    public function __construct(
        Request $request
    ) {
        $this->request = $request;
        $this->izAsset = app()['izAsset'];
        $this->izView = app()['izView'];
        $this->izTheme = app()['izTheme'];
        $this->izXml = app()['izXml'];;
        $this->theme = app()['izTheme']->getTheme();
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
        if (is_null($this->getViewFile())) {
            $viewFile = debug_backtrace()[1]['function'];
            $this->setViewFile(str_replace('/', '.', $this->request->path()) . '.' . $viewFile);
        }
        return $this->theme->scopeWithLayout($this->getViewFile(), $this->_viewData)->render();
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

    /**
     * @return string
     */
    public function getViewFile() {
        return $this->_viewFile;
    }

    /**
     * @param string $viewFile
     * @return $this
     */
    public function setViewFile($viewFile) {
        $this->_viewFile = $viewFile;

        return $this;
    }

}