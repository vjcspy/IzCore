<?php
/**
 * Created by IntelliJ IDEA.
 * User: vjcspy
 * Date: 08/09/2016
 * Time: 10:40
 */

namespace Modules\IzCore\Repositories\ImageCache;

use Intervention\Image\Filters\FilterInterface;

class ArticleTitleImage implements FilterInterface {

    /**
     * Applies filter to given image
     *
     * @param  \Intervention\Image\Image $image
     *
     * @return \Intervention\Image\Image
     */
    public function applyFilter(\Intervention\Image\Image $image) {
        // TODO: Implement applyFilter() method.
        return $image->fit(900, 475);
    }
}