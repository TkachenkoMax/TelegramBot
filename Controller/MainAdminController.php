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

    /**
     * Create new admin in application
     *
     * @param $bot
     * @return Closure
     */
    public function createAdmin($bot){
        return function ($message) use ($bot) {
            $text = trim($message->getText());

            $parameters = explode(" ", trim(str_replace("/createAdmin", "", $text)), 2);

            $new_admin_id = (int) $parameters[0];

            if ($new_admin_id == $message->getChat()->getId()) {
                $bot->sendMessage($new_admin_id, "Вы не можете назначить себя админом");
                return;
            }

            $admin_exists = AdminModel::getNotDeletedAdminById($new_admin_id);
            if (count($admin_exists) != 0) {
                $bot->sendMessage($message->getChat()->getId(), "Пользователь с таким TelegramID уже является админом");
                return;
            }

            $admin = AdminModel::getDeletedAdminById($new_admin_id);

            if (count($admin) != 0) {
                AdminModel::createAdmin($admin[0]['id']);

                $bot->sendMessage($message->getChat()->getId(), "Админ успешно создан");
                $bot->sendMessage($new_admin_id, "Вы назначены админом этого бота, проверьте какие команды стали Вам доступны написав в чат '/help'");
            } else {
                $bot->sendMessage($message->getChat()->getId(), "Нельзя назначить пользователя с таким TelegramID админом");
            }
        };        
    }

    /**
     * Delete existing admin from application
     *
     * @param $bot
     * @return Closure
     */
    public function deleteAdmin($bot){
        return function ($message) use ($bot) {
            $text = trim($message->getText());

            $parameters = explode(" ", trim(str_replace("/deleteAdmin", "", $text)), 2);

            $deleted_id = (int) $parameters[0];

            if ($deleted_id == $message->getChat()->getId()) {
                $bot->sendMessage($deleted_id, "Вы не можете лишить себя прав админа");
                return;
            }

            $admin = AdminModel::getNotDeletedAdminById($deleted_id);

            if (count($admin) != 0) {
                AdminModel::deleteAdmin($admin[0]['id']);

                $bot->sendMessage($message->getChat()->getId(), "Админ успешно удален");
                $bot->sendMessage($deleted_id, "Вы лишены должности админа");
            } else {
                $bot->sendMessage($message->getChat()->getId(), "Пользователь с таким TelegramID не является админом");
            }
        };
    }
}