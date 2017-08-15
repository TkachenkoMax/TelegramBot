<?php

/**
 * Created by PhpStorm.
 * User: max
 * Date: 05.08.17
 * Time: 10:34
 */
class UserController
{
    /**
     * Register new user in database
     * 
     * @param $telegram_id
     * @param $first_name
     * @param $last_name
     * @throws Exception
     */
    public function register($telegram_id, $first_name, $last_name){
        try {
            UserModel::register($telegram_id, $first_name, $last_name);
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage(), $ex->getCode());
        }
    }


    /**
     * Check if user set his language setting 
     * 
     * @param $telegram_id
     * @return null|mixed
     * @throws Exception
     */
    public function checkLanguage($telegram_id){
        $language = UserModel::getUserLanguage($telegram_id);
        if (!empty($language)) {
            return $language;
        }
        
        return null;
    }
    
    public function test($bot){
        return function ($message) use ($bot) {
            $num = rand(0,100);
            $answer = 'Случайное число: ' . $num;
            $bot->sendMessage($message->getChat()->getId(), $answer);
        };
    }
}