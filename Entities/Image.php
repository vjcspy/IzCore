<?php
/**
 * User: vjcspy
 * Date: 6/22/16
 * Time: 8:52 PM
 */

namespace Modules\IzCore\Entities;

/**
 * Class Image
 * See "Polymorphic Relations"
 * Image MorphTo many Entities
 *
 * @package Modules\IzCore\Entities
 */
class Image extends \Illuminate\Database\Eloquent\Model {

    /**
     * @var string
     */
    protected $table = 'izcore_image';

    /**
     * Get all of the owning imageable models.
     */
    public function imageable() {
        return $this->morphTo();
    }
}