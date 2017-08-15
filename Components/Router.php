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
            
            $answer = "Добро пожаловать, <b>$first_name $last_name</b>!
Меня зовут Катарина, я ваш умный помощник в Телеграме.
Чтобы увидеть список доступных команд и возможностей, напишите <i>/help</i>.

Но сперва лучше сообщите мне о себе немного информации:
<pre>   <i>'/setDateOfBirth &ltдата рождения&gt'</i> - указать Вашу дату рождения чтобы я никогда не забыла поздравить Вас
    <i>/setCity &ltгород&gt</i> - указать Ваш город проживания, что поможет мне в некоторых запросах
    <i>/setAlias &ltпсевдоним&gt</i> - указать Ваш псевдоним, именно так я к Вам и буду обращаться вместо $first_name $last_name
    <i>/setLanguage</i> - указать язык, на котором я буду Вам писать. На данный момент доступен только русский</pre>

<b>У Вас все получится:)</b>";
            $bot_in_func->sendMessage($message->getChat()->getId(), $answer, "HTML");
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

        $this->bot->command('setLanguage', function ($message) use ($bot_in_func) {
            $controller = new UserController();
            $telegram_id = ($message->getChat()->getId();
            
            $language = $controller->checkLanguage($telegram_id);
            if (!is_null($language)) {
                $bot_in_func->sendMessage($telegram_id, "У вас уже установлен язык - $language");
            } else {
                $bot_in_func->sendMessage($telegram_id, "Язык еще не был установлен");
            }
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

        // запускаем обработку
        $this->bot->run();
    }
}