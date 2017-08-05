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
        // TODO
    }
    
    /*public function func(){
        // обязательное. Запуск бота
        $this->bot->command('start', function ($message) use ($bot) {
            $answer = 'Добро пожаловать!';
            $bot->sendMessage($message->getChat()->getId(), $answer);
        });

// помощь
        $bot->command('help', function ($message) use ($bot) {
            $answer = 'Команды:
/help - помощь
/random - сгенерировать случайное число от 0 до 100';
            $bot->sendMessage($message->getChat()->getId(), $answer);
        });

        $bot->command('random', function ($message) use ($bot) {
            $num = rand(0,100);
            $answer = 'Случайное число: ' . $num;
            $bot->sendMessage($message->getChat()->getId(), $answer);
        });

        $bot->command('map', function ($message) use ($bot) {
            $bot->sendLocation($message->getChat()->getId(), 50, 36);
        });

        $bot->command('register', function ($message) use ($bot) {
            $db = new Database();

            try{
                $connection = $db->connect();
            } catch (PDOException $e) {
                $bot->sendMessage($message->getChat()->getId(), "Fail connection");
                die('Подключение не удалось: ' . $e->getMessage());
            }

            try {
                $stmt = $connection->prepare("INSERT INTO users (telegram_id, first_name, last_name) VALUES (?, ?, ?)");
                $stmt->bindParam(1, $message->getChat()->getId());
                $stmt->bindParam(2, $message->getChat()->getFirstName());
                $stmt->bindParam(3, $message->getChat()->getLastName());
                $result = $stmt->execute();
            } catch (PDOException $e) {
                $bot->sendMessage($message->getChat()->getId(), "Fail statement");
                die('Выполнить запрос не удалось: ' . $e->getMessage());
            }

            $bot->sendMessage($message->getChat()->getId(), "Вы успешно зарегистрированы, " . $message->getChat()->getFirstName() . " " . $message->getChat()->getLastName());
        });

        $bot->command('migrate_up', function ($message) use ($bot) {
            Migrations::up();

            $bot->sendMessage($message->getChat()->getId(), "Successfully create");
        });

        $bot->command('migrate_down', function ($message) use ($bot) {
            Migrations::down();

            $bot->sendMessage($message->getChat()->getId(), "Successfully delete");
        });

        $bot->command('users', function ($message) use ($bot) {
            $db = new Database();

            try{
                $connection = $db->connect();
            } catch (PDOException $e) {
                $bot->sendMessage($message->getChat()->getId(), "Fail connection");
                die('Подключение не удалось: ' . $e->getMessage());
            }

            $users = "Users: ";

            $stmt = $connection->query('SELECT * FROM users');
            while ($row = $stmt->fetch())
            {
                $user = $row['first_name'] . " " . $row['last_name'] . "(" . $row['telegram_id'] . ")";
                $users.=$user." | ";
            }

            $bot->sendMessage($message->getChat()->getId(), $users);
        });

// запускаем обработку
        $bot->run();
    }*/
}