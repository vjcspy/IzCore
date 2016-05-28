<?php
/**
 * Created by PhpStorm.
 * User: vjcspy
 * Date: 27/05/2016
 * Time: 16:33
 */

namespace Modules\IzCore\Repositories\Theme;


use Modules\IzCore\Repositories\Module;
use Modules\IzCore\Repositories\Object\DataObject;
use Modules\IzCore\Repositories\Theme\Asset\AdditionAsset;
use Modules\IzCore\Repositories\Theme\Asset\Dependency;
use Pingpong\Modules\Repository;
use Teepluss\Theme\Theme;

class Asset extends DataObject {

    /**
     * @var \Pingpong\Modules\Repository
     */
    protected $module;

    /**
     * Dựa vào giá trị hiện tại của router để biết được sẽ inject assets vào
     * Có rất nhiều router và khi các module khác chạy sẽ inject các asset của mình vào router nào đó
     *
     * @var bool
     */
    protected $currentPath = false;

    /**
     * @var $theme Theme
     */
    protected $theme;

    /**
     * @var \Modules\IzCore\Repositories\Module
     */
    protected $izModule;

    /**
     * Chứa array các path. Mỗi path là 1 array các class implement Modules\IzCore\Repositories\Theme\Asset\AdditionAsset
     * [
     * 'path'=>['Class implement Modules\IzCore\Repositories\Theme\Asset\AdditionAsset']
     * ]
     *
     * @var array
     */
    protected $additionAssets = [];

    /**
     * [
     * 'path' => ['asset']
     * ]
     *
     * @var array
     */
    protected $assets = [];
    /**
     * @var \modules\IzCore\Repositories\Theme\Asset\Dependency
     */
    protected $izAssetDependency;

    /**
     * Asset constructor.
     *
     * @param \Pingpong\Modules\Repository                        $module
     * @param \Modules\IzCore\Repositories\Module                 $izModule
     * @param \modules\IzCore\Repositories\Theme\Asset\Dependency $izAssetDependency
     * @param array                                               $data
     */
    public function __construct(
        Repository $module,
        Module $izModule,
        Dependency $izAssetDependency,
        array $data = []
    ) {
        $this->module            = $module;
        $this->izModule          = $izModule;
        $this->izAssetDependency = $izAssetDependency;
        parent::__construct($data);
    }

    /**
     * @return string | bool
     */
    public function getCurrentPath() {
        return $this->currentPath;
    }

    /**
     * @param mixed $currentPath
     *
     * @return $this
     */
    public function setCurrentPath($currentPath) {
        $this->currentPath = $currentPath;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTheme() {
        return $this->theme;
    }

    /**
     * @param mixed $theme
     *
     * @return $this
     */
    public function setTheme($theme) {
        $this->theme = $theme;

        return $this;
    }

    /**
     * Merge assets from another modules to current asset
     *
     * @param                       $path
     *
     * @return $this
     * @throws \Exception
     */
    public function initAdditionAssets($path = null) {
        if (is_null($path))
            $path = $this->getCurrentPath();

        if ($path) {
            /*TODO: inject assets */
            if (isset($this->additionAssets[$path])) {
                foreach ($this->additionAssets[$path] as $additionAssetClass) {

                    /** @var AdditionAsset $obj */
                    $obj = app()->make($additionAssetClass);

                    $this->assets = array_merge($this->assets, $obj->handle());
                }
            }
        }
        else
            throw new \Exception("Not found path");

        return $this;
    }

    /**
     * Thêm asset vào controller hiện tại
     *
     * @param       $path
     * @param array $assets
     *
     * @return $this
     */
    public function addAssets($path, array $assets) {
        if (!isset($this->assets[$path]))
            $this->assets[$path] = $assets;
        else
            $this->assets[$path] = array_merge($this->assets[$path], $assets);

        return $this;

    }

    /**
     * Thêm assets
     *
     * @param \Teepluss\Theme\Theme $theme
     *
     * @param null                  $currentPath
     *
     * @return $this
     * @throws \Exception
     */
    public function initAssets(Theme $theme = null, $currentPath = null) {
        if (is_null($theme))
            $theme = $this->getTheme();

        if (is_null($currentPath)) {
            if ($this->currentPath)
                $assets = isset($this->assets[$this->currentPath]) ? $this->assets[$this->currentPath] : [];
            else
                throw new \Exception("Not found current path");
        }
        else {
            $assets = isset($this->assets[$this->currentPath]) ? $this->assets[$this->currentPath] : [];
        }

        // Lấy thêm assets từ các modules khác
        $this->initAdditionAssets($this->currentPath);

        $moduleAssets = $this->izModule->getAssets();

        /*
         * TODO: Trả về thứ tự cài đặt của assets theo dependency
         */
        $assets = $this->izAssetDependency->processDependency($assets);
        /*
         * Add asset to theme
         */
        foreach ($assets as $asset) {
            if (isset($moduleAssets[$asset])) {
                if (isset($moduleAssets[$asset]['sources']['scripts'])) {
                    /*add script*/
                    foreach ($moduleAssets[$asset]['sources']['scripts'] as $k => $script) {
                        $theme->asset()->container('footer')->usePath()->add(
                            'script-' . $asset . '-' . $k,
                            $script,
                            []);
                    }
                }

                if (isset($moduleAssets[$asset]['sources']['styles'])) {
                    /*add style*/
                    foreach ($moduleAssets[$asset]['sources']['styles'] as $k => $style) {
                        $theme->asset()->usePath()->add(
                            'style-' . $asset . '-' . $k,
                            $style,
                            []);
                    }
                }
            }
            else {
                throw new \Exception('Not found asset: ' . $asset);
            }
        }

        return $this;
    }
}