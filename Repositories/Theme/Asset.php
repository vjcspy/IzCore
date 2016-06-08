<?php
/**
 * Created by PhpStorm.
 * User: vjcspy
 * Date: 27/05/2016
 * Time: 16:33
 */

namespace Modules\IzCore\Repositories\Theme;


use Modules\IzCore\Repositories\IzXml;
use Modules\IzCore\Repositories\Module as IzModule;
use Modules\IzCore\Repositories\Object\DataObject;
use Modules\IzCore\Repositories\Theme as IzTheme;
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

    protected $customAssets = [];

    /**
     * ONLY BOWER_COMPONENTS
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
     * @var \Modules\IzCore\Repositories\Theme
     */
    protected $izTheme;
    protected $izXml;

    /**
     * Asset constructor.
     *
     * @param \Pingpong\Modules\Repository                        $module
     * @param \Modules\IzCore\Repositories\Module                 $izModule
     * @param \modules\IzCore\Repositories\Theme\Asset\Dependency $izAssetDependency
     * @param \Modules\IzCore\Repositories\Theme                  $izTheme
     * @param \Modules\IzCore\Repositories\IzXml                  $izXml
     * @param array                                               $data
     */
    public function __construct(
        Repository $module,
        IzModule $izModule,
        Dependency $izAssetDependency,
        IzTheme $izTheme,
        IzXml $izXml,
        array $data = []
    ) {
        $this->izXml             = $izXml;
        $this->module            = $module;
        $this->izModule          = $izModule;
        $this->izAssetDependency = $izAssetDependency;
        $this->izTheme           = $izTheme;
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
     * Include: assets added by provider OR assets added by XML
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
            if (!isset($this->assets[$path]))
                $this->assets[$path] = [];

            /*
             * inject assets what added by Provider
             * */
            if (isset($this->additionAssets[$path])) {
                foreach ($this->additionAssets[$path] as $additionAssetClass) {

                    /** @var AdditionAsset $obj */
                    $obj = app()->make($additionAssetClass);

                    $this->assets[$path] = array_unique(array_merge($this->assets[$path], $obj->handle()));
                }
            }

            /*
             * Inject bower_components what added by xml
             * */
            $xmlPath = $this->theme->getLayoutName() . '_' . $path;
            $xml     = $this->izXml->getXmlByPath($xmlPath);
            if (isset($xml['bower_components'])) {
                $bowerAssets = [];
                foreach ($xml['bower_components'] as $bower_component) {
                    $bowerAssets[] = $bower_component['name'];
                }
                $this->assets[$path] = array_unique(array_merge($this->assets[$path], $bowerAssets));
            }
            $xmlPath = 'all';
            $xml     = $this->izXml->getXmlByPath($xmlPath);
            if (isset($xml['bower_components'])) {
                $bowerAssets = [];
                foreach ($xml['bower_components'] as $bower_component) {
                    $bowerAssets[] = $bower_component['name'];
                }
                $this->assets[$path] = array_unique(array_merge($this->assets[$path], $bowerAssets));
            }

        }
        else
            throw new \Exception("Not found path");

        return $this;
    }

    /**
     * Add assets to current path
     * Notice: assets must define in BOWER_COMPONENTS. If custom assets please use function: addCustomAssets
     * If not define current path, will add to all paths
     * Call from controller
     *
     * @param       $path
     * @param array $assets
     *
     * @return $this
     */
    public function addAssets($path = 'all', array $assets) {
        if (!isset($this->assets[$path]))
            $this->assets[$path] = $assets;
        else
            $this->assets[$path] = array_merge($this->assets[$path], $assets);

        return $this;

    }

    /**
     * ADD custom assets(css/js) It is not in BOWER_COMPONENTS
     *
     * @param       $path
     * @param array $customAssets
     *
     * @return mixed
     * @throws \Exception
     *
     */
    public function addCustomAssets($path, array $customAssets) {
        if (is_null($path))
            if (!$this->getCurrentPath())
                throw new \Exception('Must have path');
            else
                $path = $this->getCurrentPath();
        foreach ($customAssets as $customName => $customAsset) {
            if (!isset($this->customAssets[$path]))
                $this->customAssets[$path] = [];
            $this->customAssets[$path][$customName] = $customAsset;
        }

        return $this->customAssets[$path];
    }

    /**
     * Call when render layout.
     * Merger additions assets, 'all' assets, current assets
     * Resolve dependency assets
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
                $currentPath = $this->currentPath;
            else
                throw new \Exception("Not found current path");
        }
        // Lấy thêm assets từ các modules khác
        $this->initAdditionAssets($currentPath);

        $assets = isset($this->assets[$currentPath]) ? $this->assets[$currentPath] : [];

        // Merge global asset
        $assets = $this->mergeGlobalAssets($assets);

        //Get All assets in modules, not just current assets in path because they are interdependent

        $moduleAssets = $this->izTheme->getAssetsTree();

        /*
         * TODO: Trả về thứ tự cài đặt của assets theo dependency
         */
        $assets = $this->izAssetDependency->processDependency($assets);

        /*
         * Add bower_components asset to theme
         */
        foreach ($assets as $asset) {
            if (isset($moduleAssets[$asset])) {
                if (isset($moduleAssets[$asset]['sources']['scripts'])) {
                    /*add script*/
                    foreach ($moduleAssets[$asset]['sources']['scripts'] as $k => $script) {
                        $theme->asset()->container('footer')->usePath($moduleAssets[$asset]['theme_name'])->add(
                            'script-' . $asset . '-' . $k,
                            $script,
                            []);
                    }
                }

                if (isset($moduleAssets[$asset]['sources']['styles'])) {
                    /*add style*/
                    foreach ($moduleAssets[$asset]['sources']['styles'] as $k => $style) {
                        $theme->asset()->usePath($moduleAssets[$asset]['theme_name'])->add(
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

        /*
         * Add custom asset to theme
         * */
        // From Provider
        if (isset($this->customAssets[$currentPath])) {
            foreach ($this->customAssets[$currentPath] as $customAssetName => $customAsset) {
                $theme->asset()->container('custom-assets')->usePath(
                    isset($customAsset['theme_name']) ? $customAsset['theme_name'] : $this->theme->getThemeName())
                      ->add(
                          $customAssetName,
                          $customAsset['source'],
                          isset($customAsset['dependency']) ? $customAsset['dependency'] : []
                      );
            }
        }

        // From XML
        $xml = $this->izXml->getXmlByPath($this->theme->getLayoutName() . '_' . $currentPath);
        if (isset($xml['custom_assets'])) {
            foreach ($xml['custom_assets'] as $customAsset) {
                $theme->asset()->container('custom-assets')->usePath(
                    isset($customAsset['theme_name']) ? $customAsset['theme_name'] : $this->theme->getThemeName())
                      ->add(
                          $customAsset['name'],
                          $customAsset['source'],
                          isset($customAsset['dependency']) ? explode(',', $customAsset['dependency']) : []
                      );
            }
        }
        $xml = $this->izXml->getXmlByPath('all');
        if (isset($xml['custom_assets'])) {
            foreach ($xml['custom_assets'] as $customAsset) {
                $theme->asset()->container('custom-assets')->usePath(
                    isset($customAsset['theme_name']) ? $customAsset['theme_name'] : $this->theme->getThemeName())
                      ->add(
                          $customAsset['name'],
                          $customAsset['source'],
                          isset($customAsset['dependency']) ? explode(',', $customAsset['dependency']) : []
                      );
            }
        }


        return $this;
    }

    /**
     * Merge Global assets for BOWER_COMPONENTS
     *
     * @param $assets
     *
     * @return array
     */
    public function mergeGlobalAssets($assets) {
        return isset($this->assets['all']) ? array_unique(array_merge($assets, $this->assets['all'])) : $assets;
    }
}