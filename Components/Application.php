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
    private $main_admin;

    /**
     * Application constructor. Creating bot instance
     */
    public function __construct()
    {
        $bot_config = include(__ROOT__ . "/Config/bot.php");
        $this->token = $bot_config["token"];
        $this->bot = new Client($this->token);
        $this->main_admin = $bot_config["main_admin_id"];
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

        testFile($this->updates);
        
        if ($this->updates[0]->getMessage() === null) return;

        $this->user = UserModel::getBy("telegram_id", $this->updates[0]->getMessage()->getFrom()->getId())[0];

        if (!is_null($this->user) && $this->user->getIsAdmin()) {
            if ($this->user->getTelegramId() == $this->main_admin) {
                $controller = new MainAdminController();

                $this->bot->command('rebuild', $controller->rebuildDatabase($this->bot));
                $this->bot->command('createAdmin', $controller->createAdmin($this->bot));
                $this->bot->command('deleteAdmin', $controller->deleteAdmin($this->bot));
            } else
                $controller = new AdminController();

            $this->bot->command('info', $controller->sendInformation($this->bot));
        }
        else 
            $controller = new MainController();

        $this->bot->command('start', $controller->register($this->bot, $this->user));

        if ($this->user !== null) {
            $this->bot->command('help', $controller->showHelp($this->bot, $this->user));
            $this->bot->command('aboutMe', $controller->aboutMe($this->bot, $this->user));
            $this->bot->on($controller->random($this->bot, $this->user), $controller->returnTrue());
            $this->bot->command('weather', $controller->weather($this->bot, $this->user));
            
            $this->bot->command('setLanguage', $controller->setLanguage($this->bot, $this->user));
            $this->bot->command('setAlias', $controller->setAlias($this->bot, $this->user));
            $this->bot->command('setDateOfBirth', $controller->setDateOfBirth($this->bot, $this->user));
            $this->bot->on($controller->setCity($this->bot, $this->user), $controller->returnTrue());
            
            $this->bot->command('instagramLogin', $controller->instagramSetLogin($this->bot, $this->user));
            $this->bot->command('instagramPassword', $controller->instagramSetPassword($this->bot, $this->user));
            $this->bot->command('instagramPostTimelinePhoto', $controller->instagramPreparePhotoToPost($this->bot, $this->user));
            $this->bot->command('instagramPostStoryPhoto', $controller->instagramPreparePhotoToPost($this->bot, $this->user));
            $this->bot->command('instagramTimeline', $controller->instagramTimeline($this->bot, $this->user));
            $this->bot->command('instagramLikePost', $controller->instagramLikePost($this->bot, $this->user));
            $this->bot->command('instagramCommentPost', $controller->instagramCommentPost($this->bot, $this->user));
            $this->bot->on($controller->instagramPostPhoto($this->bot, $this->user, $this->token), $controller->returnTrue());
        }

        $this->bot->handle($this->updates);
    }

    /**
     * Function, where application saves every incoming update to database (except first '/start' command)
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