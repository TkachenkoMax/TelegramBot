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

    public function sendListOfUsers($bot){
        return function ($message) use ($bot) {
            $users = UserModel::all();
            $file = "files/users.txt";

            if (file_exists($file))
                unlink($file);

            $fp = fopen($file, "rw");
            fwrite($fp, "Список пользователей:\n\n");
            fclose($fp);

            foreach ($users as $user) {
                $user_info = "Имя и фамилия: " . $user->getFirstName() . " " . $user->getLastName() .
                    "\nДата рождения: " . ( is_object($user->getDateOfBirth()) ? $user->getDateOfBirth()->format("d-m-Y") : "не установлена" ) .
                    "\nГород: " . ( is_object($user->getCity()) ? $user->getCity()->getCity() : "не установлен" ) .
                    "\nЯзык: " . ( is_object($user->getTelegramLanguage()) ? $user->getTelegramLanguage()->getLanguageName() : "не установлен" ) .
                    "\nПсевдоним: " . ( $user->getAlias() !== null ? $user->getAlias() : "не установлен" );

                file_put_contents($file, $user_info, FILE_APPEND);
            }

            $zip = new ZipArchive();
            $archive_path = "files/info.zip";

            if ($zip->open($archive_path, ZipArchive::CREATE)!==TRUE) {
                exit("Невозможно открыть <$archive_path>\n");
            }

            $zip->addFile($file);
            $zip->close();

            $bot->sendDocument($message->getChat()->getId(), "http://apihelper-bot.herokuapp.com/public/files/info.zip");
        };
    }
}