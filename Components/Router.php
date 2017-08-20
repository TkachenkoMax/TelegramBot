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
    private $user;

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
    
    /**
     * Registering all updates from user
     */
    public function handle() 
    {
        $updates = $this->bot->run();
        incoming_command($updates);

        $this->user = UserModel::getBy("telegram_id", $updates[0]->getMessage()->getFrom()->getId());

        incoming_command($this->user);

        $controller = new AdminController();

        $this->bot->command('start', $controller->register($this->bot));

        $this->bot->command('help', $controller->showHelp($this->bot));

        $this->bot->on($controller->random($this->bot), $controller->returnTrue());

        $this->bot->on($controller->setLanguage($this->bot), $controller->returnTrue());

        $this->bot->command('migrate_up', $controller->migrateUp($this->bot));

        $this->bot->command('migrate_down', $controller->migrateDown($this->bot));

        $this->bot->command('seed', $controller->seed($this->bot));

        $this->bot->handle($updates);
    }
}