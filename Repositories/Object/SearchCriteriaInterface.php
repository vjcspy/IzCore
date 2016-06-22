<?php
/**
 * Created by IntelliJ IDEA.
 * User: vjcspy
 * Date: 22/06/2016
 * Time: 15:30
 */

namespace Modules\IzCore\Repositories\Object;


interface SearchCriteriaInterface {

    /**
     * Get page size.
     *
     * @return int|null
     */
    public function getPageSize();

    /**
     * Set page size.
     *
     * @param int $pageSize
     *
     * @return $this
     */
    public function setPageSize($pageSize);

    /**
     * Get current page.
     *
     * @return int|null
     */
    public function getCurrentPage();

    /**
     * Set current page.
     *
     * @param int $currentPage
     *
     * @return $this
     */
    public function setCurrentPage($currentPage);
}