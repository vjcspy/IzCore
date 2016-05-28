<?php
/**
 * Created by PhpStorm.
 * User: vjcspy
 * Date: 28/05/2016
 * Time: 12:56
 */

namespace Modules\IzCore\Repositories;


use Modules\IzCore\Repositories\Object\DataObject;
use Modules\IzCore\Repositories\Theme\View\AdditionView;

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

    public function __construct(array $data = []) {
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
                /** @var AdditionView $item */
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
}