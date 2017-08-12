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
            $answer = 'Добро пожаловать!';
            $bot_in_func->sendMessage($message->getChat()->getId(), $answer);
        });

        // помощь
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

        $this->bot->command('register', function ($message) use ($bot_in_func) {
            $connection = Database::connect();

            try {
                $stmt = $connection->prepare("INSERT INTO users (telegram_id, first_name, last_name) VALUES (?, ?, ?)");
                $stmt->bindParam(1, $message->getChat()->getId());
                $stmt->bindParam(2, $message->getChat()->getFirstName());
                $stmt->bindParam(3, $message->getChat()->getLastName());
                $result = $stmt->execute();
            } catch (PDOException $e) {
                $bot_in_func->sendMessage($message->getChat()->getId(), "Fail statement");
                die('Выполнить запрос не удалось: ' . $e->getMessage());
            }

            $bot_in_func->sendMessage($message->getChat()->getId(), "Вы успешно зарегистрированы, " . $message->getChat()->getFirstName() . " " . $message->getChat()->getLastName());
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