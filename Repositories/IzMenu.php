<?php
/**
 * Created by PhpStorm.
 * User: vjcspy
 * Date: 07/06/2016
 * Time: 12:02
 */

namespace Modules\IzCore\Repositories;


use Modules\IzCore\Repositories\Object\DataObject;

class IzMenu extends DataObject {

    /**
     * container all menu data
     *
     * @var array
     */
    protected $menus = [];
    /**
     * @var \Modules\IzCore\Repositories\IzCoreHelper
     */
    protected $izCoreHelper;

    /**
     * IzMenu constructor.
     *
     * @param \Modules\IzCore\Repositories\IzCoreHelper $izCoreHelper
     *
     * @param array                                     $data
     *
     * @internal param array $data
     */
    public function __construct(
        IzCoreHelper $izCoreHelper,
        array $data = []
    ) {
        $this->izCoreHelper = $izCoreHelper;
        parent::__construct($data);
    }

    /**
     * @param $menuName
     * @param $menuItemsData
     *
     * @return $this
     */
    public function addMenu($menuName, array $menuItemsData) {
        if (!isset($this->menus[$menuName]))
            $this->menus[$menuName] = [];
        foreach ($menuItemsData as $menuItem) {
            $this->menus[$menuName][] = $menuItem;
        }

        return $this;
    }

    /**
     * @param       $menuName
     * @param array $menu
     *
     * @return $this
     */
    public function setMenu($menuName, array $menu) {
        $this->menus[$menuName] = $menu;

        return $this;
    }

    /**
     * Get Menu by name
     *
     * @param $menuName
     *
     * @return array|mixed
     */
    public function getMenu($menuName) {
        $menu = isset($this->menus[$menuName]) ? $this->menus[$menuName] : [];

        return $this->izCoreHelper->arrangeAscArrayByPriority($menu);
    }

    /**
     * Retrieve position of parent which have name match
     *
     * @param $menu
     * @param $parentName
     *
     * @return bool|int|string
     */
    public function getParentNamePosition($menu, $parentName) {
        foreach ($menu as $position => $parent) {
            if ($parent['name_id'] == $parentName) {
                return $position;
            }
        }

        return false;
    }
}