<?php

/**
 * Created by PhpStorm.
 * User: max
 * Date: 28.08.17
 * Time: 12:24
 */

use \TelegramBot\Api\Client;

class Scheduler
{
    private $bot;
    private $token;
    
    public function __construct()
    {
        $bot_config = include(__ROOT__ . "/Config/bot.php");
        $this->token = $bot_config["token"];
        $this->bot = new Client($this->token);
    }
    
    public function runTask(){
        $this->bot->sendMessage(382994855, "тест планировщика");
    }
}