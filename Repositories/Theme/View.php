<?php
/**
 * Created by PhpStorm.
 * User: vjcspy
 * Date: 27/05/2016
 * Time: 17:55
 */

namespace Modules\IzCore\Repositories\Theme;

use Modules\IzCore\Repositories\IzXml;
use Modules\IzCore\Repositories\Module;
use Pingpong\Modules\Repository;
use Modules\IzCore\Repositories\Object\DataObject;

class View extends DataObject {

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
     * @var \Modules\IzCore\Repositories\Module
     */
    protected $izModule;

    /**
     * [
     * 'path'=>['Modules\IzCore\Repositories\Theme\View\AdditionView']
     * ]
     *
     * @var array
     */
    protected $viewsData = [];
    /**
     * @var \Modules\IzCore\Repositories\IzXml
     */
    protected $izXml;

    /**
     * @return string | bool
     */

    /**
     * Asset constructor.
     *
     * @param \Pingpong\Modules\Repository        $module
     * @param \Modules\IzCore\Repositories\Module $izModule
     * @param \Modules\IzCore\Repositories\IzXml  $izXml
     * @param array                               $data
     */
    public function __construct(
        Repository $module,
        Module $izModule,
        IzXml $izXml,
        array $data = []
    ) {
        $this->izXml    = $izXml;
        $this->module   = $module;
        $this->izModule = $izModule;
        parent::__construct($data);
    }

    /**
     * @return bool
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
     * Dành cho các module ngoài muốn add data vào 1 path nào đó
     *
     * @param $path
     * @param $data
     */
    public function addAdditionData($path, $data) {
        foreach ($data as $item) {
            if (!isset($this->viewsData[$path]))
                $this->viewsData[$path] = [];

            $this->viewsData[$path][] = $item;
        }
    }

    /**
     * Merge data from another module to current view
     *
     * @param $viewData
     * @param $path
     *
     * @return $this
     */
    public function initAdditionViews(&$viewData, $path) {
        if ($path) {
            $this->initViewDataFromXml($path);
            if (isset($this->viewsData[$path]))
                foreach ($this->viewsData[$path] as $obj) {
                    /** @var \Modules\IzCore\Repositories\Theme\View\AdditionViewInterface $obj */
                    $obj      = app()->make($obj);
                    $viewData = array_merge($viewData, $obj->handle());
                }
        }

        return $this;
    }

    public function setLayout($layoutName) {
        $this->setData('theme_layout', $layoutName);

        return $this;
    }

    public function initViewDataFromXml($path) {
        $xml = $this->izXml->getXmlByPath($this->getThemeLayout() . '_' . $path);
        if (isset($xml['view_data'])) {
            if (!isset($this->viewsData[$path]))
                $this->viewsData[$path] = [];
            foreach ($xml['view_data'] as $viewClass) {
                $this->viewsData[$path][] = $viewClass['name'];
            }
        }

        return $this;
    }
}