<?php
/**
 * Created by PhpStorm.
 * User: vjcspy
 * Date: 27/05/2016
 * Time: 17:55
 */

namespace Modules\IzCore\Repositories\Theme;

use Modules\IzCore\Repositories\Module;
use Pingpong\Modules\Repository;
use Teepluss\Theme\Theme;
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
     * @return string | bool
     */

    /**
     * Asset constructor.
     *
     * @param \Pingpong\Modules\Repository        $module
     * @param \Modules\IzCore\Repositories\Module $izModule
     * @param array                               $data
     */
    public function __construct(
        Repository $module,
        Module $izModule,
        array $data = []
    ) {
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
     * Merge data from another module to current view
     *
     * @param $viewData
     * @param $path
     *
     * @return $this
     */
    public function initAdditionViews(&$viewData, $path) {
        if ($path) {
            if (isset($this->viewsData[$path]))
                foreach ($this->viewsData[$path] as $obj) {
                    /** @var \Modules\IzCore\Repositories\Theme\View\AdditionView $obj */
                    $viewData = array_merge($viewData, $obj->handle());
                }
        }

        return $this;
    }
}