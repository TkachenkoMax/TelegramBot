<?php

/**
 * Created by PhpStorm.
 * User: max
 * Date: 05.08.17
 * Time: 10:34
 */
class MainController
{
    /**
     * Register new user
     * 
     * @param $bot
     * @return Closure
     */
    public function register($bot){
        return function ($message) use ($bot) {
            $telegram_id = $message->getChat()->getId();
            $first_name = $message->getChat()->getFirstName();
            $last_name = $message->getChat()->getLastName();

            try{
                UserModel::register($telegram_id, $first_name, $last_name);
            } catch (Exception $ex) {
                if ($ex->getMessage() == "Bad registration") {
                    $bot->sendMessage($message->getChat()->getId(), "Bad registration");
                }
            }

            $answer = "Добро пожаловать, <b>$first_name $last_name</b>!
Меня зовут Катарина, я ваш умный помощник в Телеграме.
Чтобы увидеть список доступных команд и возможностей, напишите <i>/help</i>.

Но сперва лучше сообщите мне о себе немного информации:

1) <i>/setDateOfBirth &ltдата рождения&gt</i> - указать Вашу дату рождения чтобы я никогда не забыла поздравить Вас

2) <i>/setCity &ltгород&gt</i> - указать Ваш город проживания, что поможет мне в некоторых запросах

3) <i>/setAlias &ltпсевдоним&gt</i> - указать Ваш псевдоним, именно так я к Вам и буду обращаться вместо $first_name $last_name

4) <i>/setLanguage</i> - указать язык, на котором я буду Вам писать. На данный момент доступен только русский

<b>У Вас все получится:)</b>";
            $bot->sendMessage($message->getChat()->getId(), $answer, "HTML");
        };
    }

    /**
     * Calculate random number
     * 
     * @param $bot
     * @return Closure
     */
    public function random($bot){
        return function ($message) use ($bot) {
            $num = rand(0,100);
            $answer = 'Случайное число: ' . $num;
            $bot->sendMessage($message->getChat()->getId(), $answer);
        };
    }

    /**
     * Show to user message with help
     * 
     * @param $bot
     * @return Closure
     */
    public function showHelp($bot){
        return function ($message) use ($bot) {
            $answer = 'Команды:
/help - помощь
/random - сгенерировать случайное число от 0 до 100';
            $bot->sendMessage($message->getChat()->getId(), $answer);
        };
    }
    
    public function setLanguage($bot){
        return function ($message) use ($bot) {
            $telegram_id = $message->getChat()->getId();

            $language = $this->checkLanguage($telegram_id);
            if (!is_null($language)) {
                $bot->sendMessage($telegram_id, "У вас уже установлен язык - $language");
            } else {
                $bot->sendMessage($telegram_id, "Язык еще не был установлен");
            }

            $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup([[["/setLanguageRussian", "text" => "Русский"], ["/setLanguageEnglish", "text" => "English"]]], true, true);

            $bot->sendMessage($message->getChat()->getId(), "Выберите язык:", false, null,null, $keyboard);
        };
    }

    /**
     * Check if user set his language setting
     *
     * @param $telegram_id
     * @return null|mixed
     * @throws Exception
     */
    private function checkLanguage($telegram_id){
        $language = UserModel::getUserLanguage($telegram_id);
        if (!empty($language)) {
            return $language;
        }

        return null;
    }
}