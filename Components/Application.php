<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 05.08.17
 * Time: 9:26
 */

use \TelegramBot\Api\Client;

class Application {
    private $bot;
    private $token;
    private $updates;
    private $user;
    private $app_languages;

    /**
     * Application constructor. Creating bot instance
     */
    public function __construct()
    {
        $bot_config = include(__ROOT__ . "/Config/bot.php");
        $this->app_languages = $bot_config['available_languages'];
        $this->token = $bot_config["token"];
        $this->bot = new Client($this->token);
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
        $this->updates = $this->bot->run();

        $this->user = UserModel::getBy("telegram_id", $this->updates[0]->getMessage()->getFrom()->getId())[0];

        if (!is_null($this->user) && $this->user->getIsAdmin()) {
            $controller = new AdminController();

            $this->bot->command('migrate_up', $controller->migrateUp($this->bot));
            $this->bot->command('migrate_down', $controller->migrateDown($this->bot));
            $this->bot->command('seed', $controller->seed($this->bot));
        }
        else 
            $controller = new MainController();

        $this->bot->command('start', $controller->register($this->bot, $this->user));
        if ($this->user !== null) {
            $this->bot->command('help', $controller->showHelp($this->bot, $this->user));
            $this->bot->on($controller->random($this->bot, $this->user), $controller->returnTrue());
            $this->bot->on($controller->setLanguage($this->bot, $this->user, $this->app_languages), $controller->returnTrue());
            $this->bot->on($controller->setAlias($this->bot, $this->user), $controller->returnTrue());
            $this->bot->on($controller->setDateOfBirth($this->bot, $this->user), $controller->returnTrue());
            $this->bot->on($controller->setCity($this->bot, $this->user), $controller->returnTrue());
            $this->bot->command('weather', $controller->weather($this->bot, $this->user));
        }

        $this->bot->handle($this->updates);
    }

    /**
     * Function, where application saves every incoming update to database
     *
     * @throws Exception
     */
    public function saveUpdateToDatabase(){
        if (!is_null($this->user)){
            $data = array("id_user" => $this->user->getId(),
                "message_id" => $this->updates[0]->getMessage()->getMessageId(),
                "text_of_message" => $this->updates[0]->getMessage()->getText()
            );
            UpdateModel::saveUpdate($data);
        }
    }
}