<?php
/**
 * Created by PhpStorm.
 * User: vjcspy
 * Date: 28/05/2016
 * Time: 12:56
 */

namespace Modules\IzCore\Repositories;


use Modules\IzCore\Repositories\Object\DataObject;
use Modules\IzCore\Repositories\Theme\View\AdditionViewInterface;
use Pingpong\Modules\Repository;

class Theme extends DataObject {

    /**
     * @var
     */
    protected $currentPath;
    /**
     * [
     *'path'=>['Modules\IzCore\Repositories\Theme\View\AdditionView]
     * ]
     *
     * @var array
     */
    protected $additionData = [];

    /**
     * [
     *'path'=>[]
     * ]
     *
     * @var array
     */
    protected $data = [];
    /**
     * @var \Pingpong\Modules\Repository
     */
    protected $module;

    /**
     * All Asset
     *
     * @var
     */
    private $assets;

    public function __construct(
        Repository $module,
        array $data = []
    ) {
        $this->module = $module;
        parent::__construct($data);
    }

    /**
     * Dành cho các module ngoài muốn add data vào 1 path nào đó
     *
     * @param $path
     * @param $data
     */
    public function addAdditionData($path, $data) {
        foreach ($data as $item) {
            if (!isset($this->additionData[$path]))
                $this->additionData[$path] = [];

            $this->additionData[$path][] = $item;
        }
    }

    /**
     * Merge data from another modules to current path
     *
     * @param null $path
     *
     * @return $this
     */
    public function initAdditionData($path = null) {
        if (is_null($path))
            $path = $this->getCurrentPath();

        if (isset($this->additionData[$path])) {
            foreach ($this->additionData[$path] as $item) {
                /** @var AdditionViewInterface $item */
                $item = app()->make($item);
                if (!isset($this->data[$path]))
                    $this->data[$path] = [];

                $this->data[$path] = array_merge($this->data[$path], $item->handle());
            }
        }

        return $this;
    }

    /**
     * @return mixed
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
     * Set data to view of current path
     *
     * @param \Teepluss\Theme\Theme $theme
     * @param                       $path
     *
     * @return $this
     */
    public function initViewData(\Teepluss\Theme\Theme $theme, $path) {
        /*Merge from another modules*/
        $this->initAdditionData($path);

        if (isset($this->data[$path])) {
            foreach ($this->data[$path] as $items) {
                foreach ($items as $k => $item) {
                    $theme->set($k, $item);
                }
            }
        }

        return $this;
    }

    /**
     * Get all assets in each theme in each module
     *
     * @return array
     * @throws \Exception
     */
    public function getAssetsTree() {
        if (is_null($this->assets)) {
            $this->assets = [];

            $pathModules = $this->module->getPath();
            $moduleDirs  = scandir($pathModules);
            foreach ($moduleDirs as $moduleDir) {
                if (!in_array($moduleDir, [".", ".."])) {
                    /*Path Config/Vendor của module hiện tại*/
                    $currentModuleThemePaths = $pathModules . '/' . $moduleDir . '/themes';

                    /*Kiểm tra xem module hiện tại có thư mục themes không*/
                    if (!file_exists($currentModuleThemePaths))
                        continue;

                    $themePath = scandir($currentModuleThemePaths);

                    foreach ($themePath as $themDir) {
                        if (!in_array($themDir, [".", ".."])) {
                            $currentThemeDir = $currentModuleThemePaths . '/' . $themDir . '/config.php';

                            // Check file config.php existed

                            if (!file_exists($currentThemeDir))
                                continue;

                            $themeConfig = (include $currentThemeDir);

                            if (isset($themeConfig['assets'])) {
                                $assetWithThemeName = [];
                                foreach ($themeConfig['assets'] as $k => $asset) {
                                    $asset['theme_name']    = $themDir;
                                    $assetWithThemeName[$k] = $asset;
                                }
                                $this->assets = array_merge($this->assets, $assetWithThemeName);
                            }
                        }
                    }
                }
            }
        }

        return $this->assets;
    }
}