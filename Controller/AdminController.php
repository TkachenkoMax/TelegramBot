<?php

/**
 * Created by PhpStorm.
 * User: max
 * Date: 12.08.17
 * Time: 13:53
 */
class AdminController
{
    public function migrateUp($bot){
       return function ($message) use ($bot) {
           $connection = Database::connect();

           Migrations::up($connection);

           $bot->sendMessage($message->getChat()->getId(), "Tables successfully create");
       }; 
    }
    
    public function migrateDown($bot) {
        function ($message) use ($bot) {
            $connection = Database::connect();

            Migrations::down($connection);

            $bot->sendMessage($message->getChat()->getId(), "Tables successfully delete");
        };
    }
    
    public function seed($bot) {
        function ($message) use ($bot) {
            $connection = Database::connect();

            Seeds::seeding($connection);

            $bot->sendMessage($message->getChat()->getId(), "Successful seeding");
        };
    }
}