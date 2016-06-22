<?php
/**
 * Created by IntelliJ IDEA.
 * User: vjcspy
 * Date: 22/06/2016
 * Time: 15:32
 */

namespace Modules\IzCore\Repositories\Object;


class SearchCriteria extends IzObject implements SearchCriteriaInterface {

    const PAGE_SIZE = 'page_size';
    const CURRENT_PAGE = 'current_page';

    /**
     * Get page size.
     *
     * @return int|null
     */
    public function getPageSize() {
        // TODO: Implement getPageSize() method.
        $this->getData(self::PAGE_SIZE);
    }

    /**
     * Set page size.
     *
     * @param int $pageSize
     *
     * @return $this
     */
    public function setPageSize($pageSize) {
        // TODO: Implement setPageSize() method.
        $this->setData(self::PAGE_SIZE, $pageSize);

        return $this;
    }

    /**
     * Get current page.
     *
     * @return int|null
     */
    public function getCurrentPage() {
        // TODO: Implement getCurrentPage() method.
        return $this->getData(self::CURRENT_PAGE);
    }

    /**
     * Set current page.
     *
     * @param int $currentPage
     *
     * @return $this
     */
    public function setCurrentPage($currentPage) {
        // TODO: Implement setCurrentPage() method.
        $this->setData(self::CURRENT_PAGE, $currentPage);

        return $this;
    }
}