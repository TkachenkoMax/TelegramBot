<?php

/**
 * Created by PhpStorm.
 * User: max
 * Date: 11.08.17
 * Time: 21:48
 */
use TelegramBot\Api\Client as Bot;

class BotTest
{
    public static function sendMessage(){
        $token = "390875011:AAEkDpRCBeIiRQ3clQ03PcEDty5QzEwtb60";
        $bot = new Bot($token);

        // если бот еще не зарегистрирован - регистрируем
        if (!file_exists("registered.trigger")) {
            /**
             * файл registered.trigger будет создаваться после регистрации бота.
             * если этого файла нет значит бот не зарегистрирован
             */

            // URl текущей страницы
            $page_url = "https://" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
            $result = $bot->setWebhook($page_url);
            if ($result) {
                file_put_contents("registered.trigger", time()); // создаем файл дабы прекратить повторные регистрации
            }
        }

        $message = "File: " . __FILE__ . ", method: " . __METHOD__ . ", line:" . __LINE__;

        $bot->sendMessage(382994855, $message);
        // запускаем обработку
        $bot->run();
    }
}