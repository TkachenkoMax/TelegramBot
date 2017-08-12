<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 05.08.17
 * Time: 9:26
 */

class Router {

    private $bot;
    private $token;

    /**
     * Router constructor. Creating bot instance
     */
    public function __construct()
    {
        $bot_config = include(__ROOT__ . "/Config/bot.php");
        $this->token = $bot_config["token"];
        $this->bot = new \TelegramBot\Api\Client($this->token);
    }


    /**
     * If we register a bot, a file 'registered.trigger' appears
     */
    public function registerBot()
    {
        if (!file_exists(__ROOT__ . "/registered.trigger")) {
            $page_url = "https://" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
            $result = $this->bot->setWebhook($page_url);
            if ($result) {
                file_put_contents("registered.trigger", time()); // Create file if not exists
            }
        }
    }
    
    public function handle() 
    {
        $this->registerBot();

        // обязательное. Запуск бота
        $bot_in_func = $this->bot;
        $this->bot->command('start', function ($message) use ($bot_in_func) {
            $controller = new UserController();
            
            $telegram_id = $message->getChat()->getId();
            $first_name = $message->getChat()->getFirstName();
            $last_name = $message->getChat()->getLastName();
            
            try{
                $controller->register($telegram_id, $first_name, $last_name);
            } catch (Exception $ex) {
                if ($ex->getMessage() == "Bad registration") {
                    $bot_in_func->sendMessage($message->getChat()->getId(), "Bad registration");
                }
            }
            
            $answer = "Добро пожаловать, $first_name $last_name!
Меня зовут Катарина, я ваш умный помощник в Телеграме.
Чтобы увидеть список доступных команд и возможностей, напишите '/help.

Но сперва лучше сообщите мне о себе немного информации:
'/setDateOfBirth <дата рождения>' - указать Вашу дату рождения чтобы я никогда не забыла поздравить Вас
'/setCity <город>' - указать Ваш город проживания, что поможет мне в некоторых запросах
'/setAlias <псевдоним>' - указать Ваш псевдоним, именно так я к Вам и буду обращаться вместо $first_name $last_name
'/setLanguage <язык>' - указать язык, на котором я буду Вам писать. На данный момент доступен только русский

У Вас все получится:)";
            $bot_in_func->sendMessage($message->getChat()->getId(), $answer);
        });

        $this->bot->command('help', function ($message) use ($bot_in_func) {
            $answer = 'Команды:
/help - помощь
/random - сгенерировать случайное число от 0 до 100';
            $bot_in_func->sendMessage($message->getChat()->getId(), $answer);
        });

        $this->bot->command('random', function ($message) use ($bot_in_func) {
            $num = rand(0,100);
            $answer = 'Случайное число: ' . $num;
            $bot_in_func->sendMessage($message->getChat()->getId(), $answer);
        });

        $this->bot->command('migrate_up', function ($message) use ($bot_in_func) {
            $connection = Database::connect();
            
            Migrations::up($connection);

            $bot_in_func->sendMessage($message->getChat()->getId(), "Tables successfully create");
        });

        $this->bot->command('migrate_down', function ($message) use ($bot_in_func) {
            $connection = Database::connect();

            Migrations::down($connection);

            $bot_in_func->sendMessage($message->getChat()->getId(), "Tables successfully delete");
        });

        $this->bot->command('seed', function ($message) use ($bot_in_func) {
            $connection = Database::connect();

            Seeds::seeding($connection);

            $bot_in_func->sendMessage($message->getChat()->getId(), "Successful seeding");
        });

        $this->bot->command('tables', function ($message) use ($bot_in_func) {
            $connection = Database::connect();

            $query = $connection->query("SHOW TABLES");
            $tables = $query->fetchAll(PDO::FETCH_COLUMN);
            $list = "Tables: ";
            foreach ($tables as $table){
                $list .="$table | ";
            }

            $bot_in_func->sendMessage($message->getChat()->getId(), "$list");
        });

        $this->bot->command('users', function ($message) use ($bot_in_func) {
            $connection = Database::connect();

            $users = "Users: ";

            $stmt = $connection->query('SELECT * FROM users');
            while ($row = $stmt->fetch())
            {
                $user = $row['first_name'] . " " . $row['last_name'] . "(" . $row['telegram_id'] . ")";
                $users.=$user." | ";
            }

            $bot_in_func->sendMessage($message->getChat()->getId(), $users);
        });

        // запускаем обработку
        $this->bot->run();
    }
}