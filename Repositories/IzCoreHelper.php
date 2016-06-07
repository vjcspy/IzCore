<?php
/**
 * Created by PhpStorm.
 * User: vjcspy
 * Date: 07/06/2016
 * Time: 14:14
 */

namespace Modules\IzCore\Repositories;


use Modules\IzCore\Repositories\Object\DataObject;

class IzCoreHelper extends DataObject {

    /**
     * Example array:
     * [
     * [
     * 'name'     => 'Dashboard',
     * 'url'      => '',
     * 'badge'    => '3',
     * 'priority' => 0,
     * 'children' => [
     * [
     * 'name'     => 'Analysis',
     * 'url'      => 'app.analysis',
     * 'priority' => 1
     * ]
     * ]
     * ],
     * [
     * 'name'     => 'System',
     * 'url'      => 'system',
     * 'priority' => 5,
     * 'children' => [
     * [
     * 'name'     => 'Currency',
     * 'url'      => 'system.currency',
     * 'priority' => 4
     * ],
     * [
     * 'name'     => 'Currency',
     * 'url'      => 'system.facebook',
     * 'priority' => 1
     * ],
     * [
     * 'name'     => 'Shop',
     * 'url'      => 'system.shop',
     * 'priority' => 1,
     * 'children' => [
     * [
     * 'name'     => 'Configuration',
     * 'url'      => 'system.shop.config',
     * 'priority' => 10
     * ],
     * [
     * 'name'     => 'Example',
     * 'url'      => 'system.shop.example',
     * 'priority' => 1
     * ]
     * ]
     * ]
     *
     * ]
     * ]
     * ]
     * 
     * @param array $array
     * @param int   $nKey
     *
     * @return array
     */
    public function arrangeAscArrayByPriority(array &$array, $nKey = 1) {
        // To work with associate array
        $key = 0;

        foreach ($array as $item) {
            /*If isset children, will arrange children first*/
            if (isset($item['children'])) {
                $array[$key]['children'] = $this->arrangeAscArrayByPriority($item['children']);
            }

            /*Break loop*/
            if ($key >= (count($array) - 1)) {
                break;
            }

            if (floatval($item['priority']) > floatval($array[$key + 1]['priority'])) {
                $array[$key]     = $array[$key + 1];
                $array[$key + 1] = $item;

                return $this->arrangeAscArrayByPriority($array, $nKey);
            }
            $key++;
        }

        if ($nKey < (count($array) - 1))
            $this->arrangeAscArrayByPriority($array, $nKey + 1);

        return $array;
    }
}