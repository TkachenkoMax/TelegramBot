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
        $controller = new MainController();

        $this->bot->command('start', $controller->register($this->bot));

        $this->bot->command('help', $controller->showHelp($this->bot));
        
        $this->bot->command('random', $controller->random($this->bot));

        $this->bot->command('setLanguage', $controller->setLanguage($this->bot));
        

        /*$this->bot->command('migrate_up', function ($message) use ($bot_in_func) {
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
        });*/

        // запускаем обработку
        $this->bot->run();
    }
}