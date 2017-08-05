<?php

/**
 * Created by PhpStorm.
 * User: max
 * Date: 05.08.17
 * Time: 11:30
 */
abstract class Model
{
    /**
     * Initialize entity
     * 
     * @param array $data
     * @return mixed
     */
    public static abstract function init($data = array());

    /**
     * Find entity by id
     * 
     * @param $id
     * @return mixed
     */
    public static abstract function getById($id);

    /**
     * Get all entities from database
     * 
     * @return mixed
     */
    public static abstract function all();
}