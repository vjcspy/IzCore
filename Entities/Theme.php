<?php
/**
 * Created by IntelliJ IDEA.
 * User: vjcspy
 * Date: 23/06/2016
 * Time: 10:45
 */

namespace Modules\IzCore\Entities;


/**
 * Class Theme
 *
 * @package Modulesodules\IzCore\Entities
 */
class Theme extends \Illuminate\Database\Eloquent\Model {

    const TYPE_ADMIN = 'admin.theme';
    const TYPE_FRONTEND = 'admin.frontend';

    /**
     * @var string
     */
    protected $table = 'izcore_theme';
    /**
     * @var array
     */
    protected $fillable = ['name', 'type'];
}