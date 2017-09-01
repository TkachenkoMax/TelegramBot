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
     * Command to delete tables, create tables and insert default values into tables in database
     *
     * @param $bot
     * @return Closure
     */
    public function rebuildDatabase($bot){
        return function ($message) use ($bot) {
            $users = UserModel::all();
            foreach ($users as $user) {
                $bot->sendMessage($user->getTelegramId(), "База данных бота была сброшена, зарегистрируйтесь снова командой '/start'");
            }
            
            $connection = Database::connect();

            Migrations::down($connection);
            $bot->sendMessage($message->getChat()->getId(), "Tables successfully delete");
            Migrations::up($connection);
            $bot->sendMessage($message->getChat()->getId(), "Tables successfully create");
            Seeds::seeding($connection);
            $bot->sendMessage($message->getChat()->getId(), "Successful seeding");
        };
    }
}