<?php
/**
 * Created by IntelliJ IDEA.
 * User: vjcspy
 * Date: 6/8/16
 * Time: 10:56 AM
 */

namespace Modules\IzCore\Repositories\Object;


abstract class IzObject extends DataObject {

    /**
     * Get hash key by array
     * @param array $arrKey
     *
     * @return string
     */
    protected function getKey(array $arrKey) {
        $hash = '';
        foreach ($arrKey as $item) {
            if (is_array($item))
                $item = $this->getKey($item);
            $hash .= $item;
        }

        return md5($hash);
    }
}