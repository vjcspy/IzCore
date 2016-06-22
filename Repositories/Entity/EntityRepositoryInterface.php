<?php
/**
 * Created by IntelliJ IDEA.
 * User: vjcspy
 * Date: 22/06/2016
 * Time: 15:20
 */

namespace Modules\IzCore\Repositories\Entity;


use Modules\IzCore\Repositories\Object\SearchCriteriaInterface;

interface EntityRepositoryInterface {

    /**
     * @param \modules\IzCore\Repositories\Object\SearchCriteriaInterface $criteriaInterface
     *
     * @return  \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getList(SearchCriteriaInterface $criteriaInterface);

    /**
     * @param integer $id
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getById($id);

    /**
     * @param $id
     *
     * @return boolean
     */
    public function deleteById($id);


    /**
     * @param [] $data
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function save($data);

}