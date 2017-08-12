<?php

/**
 * Created by PhpStorm.
 * User: max
 * Date: 05.08.17
 * Time: 10:34
 */
class UserController
{
    public function register($telegram_id, $first_name, $last_name){
        try {
            UserModel::register($telegram_id, $first_name, $last_name);
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage(), $ex->getCode());
        }
    }
}