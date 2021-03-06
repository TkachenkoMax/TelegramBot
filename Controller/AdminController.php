<?php

/**
 * Created by PhpStorm.
 * User: max
 * Date: 12.08.17
 * Time: 13:53
 */
class AdminController extends MainController
{
    /**
     * Command to send ZIP archive with information from database to administrator
     *
     * @param $bot
     * @return Closure
     */
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
                $date = $date->format('Y-m-d H:i:s');
                $update_info = "Сообщение от " . $update['first_name'] . " " . $update['last_name'] . " (Telegram ID - " . $update['telegram_id'] . ")" .
                    "\nОт " . $date .
                    "\nНомер сообщения: " . $update['message_id'] .
                    "\nТекст сообщения: " . (is_null($update['text_of_message']) ? "пустое сообщение или файл" : $update['text_of_message']);

                file_put_contents($updates_file, $update_info, FILE_APPEND);

                if(next($updates)) {
                    file_put_contents($updates_file, "\n\n", FILE_APPEND);
                }
            }

            $admins = AdminModel::getAdminsWithUsers();
            $admins_file = "files/admins" . time() . ".txt";

            file_put_contents($admins_file, "Список действующих администраторов:\n");

            $counter = 1;

            for($i = 0; $i<count($admins); $i++) {
                if (is_null($admins[$i]['deleted_at'])) {
                    $current_admin_info = $counter++ . ") " . $admins[$i]['first_name'] . " " . $admins[$i]['last_name'] . " (Telegram ID - " . $admins[$i]['telegram_id'] . ")";

                    file_put_contents($admins_file, $current_admin_info, FILE_APPEND);

                    if ($i < (count($admins) - 1)) {
                        file_put_contents($admins_file, "\n", FILE_APPEND);
                    }
                }
            }

            file_put_contents($admins_file, "\n\nИстория администрирования:\n", FILE_APPEND);

            foreach ($admins as $admin){
                $start = new DateTime($admin['created_at']);
                $start = $start->format('Y-m-d');
                $end = is_null($admin['deleted_at']) ? "..." : new DateTime($admin['deleted_at']);
                if ($end instanceof DateTime)
                    $end = $end->format('Y-m-d');
                $admin_info = "Администратор " . $admin['first_name'] . " " . $admin['last_name'] . " (Telegram ID - " . $admin['telegram_id'] . ")" .
                    "\nПериод администрирования: " . $start . " - " . $end;

                file_put_contents($admins_file, $admin_info, FILE_APPEND);

                if(next($admins)) {
                    file_put_contents($admins_file, "\n\n", FILE_APPEND);
                }
            }

            $zip = new ZipArchive();
            $archive_path = "files/info" . time() . ".zip";

            if ($zip->open($archive_path, ZipArchive::CREATE)!==TRUE) {
                exit("Невозможно открыть <$archive_path>\n");
            }

            $zip->addFile($users_file, "users.txt");
            $zip->addFile($updates_file, "updates.txt");
            $zip->addFile($admins_file, "admins.txt");
            $zip->close();

            $bot->sendDocument($message->getChat()->getId(), $_SERVER["SERVER_NAME"] . "/public/" . $archive_path);

            unlink($archive_path);
            unlink($users_file);
            unlink($updates_file);
            unlink($admins_file);
        };
    }
}