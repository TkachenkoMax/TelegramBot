<?php

/**
 * Created by PhpStorm.
 * User: max
 * Date: 12.08.17
 * Time: 13:53
 */
class AdminController extends MainController
{
    public function migrateUp($bot){
       return function ($message) use ($bot) {
           $connection = Database::connect();

           Migrations::up($connection);

           $bot->sendMessage($message->getChat()->getId(), "Tables successfully create");
       }; 
    }
    
    public function migrateDown($bot) {
        return function ($message) use ($bot) {
            $connection = Database::connect();

            Migrations::down($connection);

            $bot->sendMessage($message->getChat()->getId(), "Tables successfully delete");
        };
    }
    
    public function seed($bot) {
        return function ($message) use ($bot) {
            $connection = Database::connect();

            Seeds::seeding($connection);

            $bot->sendMessage($message->getChat()->getId(), "Successful seeding");
        };
    }

    public function sendInformation($bot){
        return function ($message) use ($bot) {
            $users = UserModel::all();
            $users_file = "files/users" . time() . ".txt";

            file_put_contents($users_file, "Список пользователей:\n");

            foreach ($users as $user) {
                $user_info = "Имя и фамилия: " . $user->getFirstName() . " " . $user->getLastName() .
                    "\nДата рождения: " . ( is_object($user->getDateOfBirth()) ? $user->getDateOfBirth()->format("d-m-Y") : "не установлена" ) .
                    "\nГород: " . ( is_object($user->getCity()) ? $user->getCity()->getCity() : "не установлен" ) .
                    "\nЯзык: " . ( is_object($user->getTelegramLanguage()) ? $user->getTelegramLanguage()->getLanguageName() : "не установлен" ) .
                    "\nПсевдоним: " . ( $user->getAlias() !== null ? $user->getAlias() : "не установлен" );

                file_put_contents($users_file, $user_info, FILE_APPEND);

                if(next($users)) {
                    file_put_contents($users_file, "\n\n", FILE_APPEND);
                }
            }

            $updates = UpdateModel::getUpdatesWithUsers();
            $updates_file = "files/updates" . time() . ".txt";

            file_put_contents($updates_file, "Список пришедших запросов от пользователей:\n");

            foreach ($updates as $update) {
                $date = new DateTime($update['created_at']);
                $update_info = "Сообщение от " . $update['first_name'] . " " . $update['last_name'] . " (Telegram ID - " . $update['telegram_id'] . ")" .
                    "\nОт " . $date->format('Y-m-d H:i:s') .
                    "\nНомер сообщения: " . $update['message_id'] .
                    "\nТекст сообщения: " . (is_null($update['text_of_message']) ? "пустое сообщение или файл" : $update['text_of_message']);

                file_put_contents($updates_file, $update_info, FILE_APPEND);

                if(next($updates)) {
                    file_put_contents($updates_file, "\n\n", FILE_APPEND);
                }
            }

            testFile($updates);

            $zip = new ZipArchive();
            $archive_path = "files/info" . time() . ".zip";

            if ($zip->open($archive_path, ZipArchive::CREATE)!==TRUE) {
                exit("Невозможно открыть <$archive_path>\n");
            }

            $zip->addFile($users_file, "users.txt");
            $zip->addFile($updates_file, "updates.txt");
            $zip->close();

            $bot->sendDocument($message->getChat()->getId(), $_SERVER["SERVER_NAME"] . "/public/" . $archive_path);

            unlink($archive_path);
            unlink($users_file);
            unlink($updates_file);
        };
    }
}