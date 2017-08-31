<?php

/**
 * Created by PhpStorm.
 * User: max
 * Date: 31.08.17
 * Time: 22:13
 */
class MainAdminController extends AdminController
{
    /**
     * Command to create tables in database
     *
     * @param $bot
     * @return Closure
     */
    public function migrateUp($bot){
        return function ($message) use ($bot) {
            $connection = Database::connect();

            Migrations::up($connection);

            $bot->sendMessage($message->getChat()->getId(), "Tables successfully create");
        };
    }

    /**
     * Command to delete tables in database
     *
     * @param $bot
     * @return Closure
     */
    public function migrateDown($bot) {
        return function ($message) use ($bot) {
            $connection = Database::connect();

            Migrations::down($connection);

            $bot->sendMessage($message->getChat()->getId(), "Tables successfully delete");
        };
    }

    /**
     * Command to insert basic values in tables of database
     *
     * @param $bot
     * @return Closure
     */
    public function seed($bot) {
        return function ($message) use ($bot) {
            $connection = Database::connect();

            Seeds::seeding($connection);

            $bot->sendMessage($message->getChat()->getId(), "Successful seeding");
        };
    }
}