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
        return function($update) use ($bot){
            $message = $update->getMessage();
            $text = trim($message->getText());
            $id = $message->getChat()->getId();

            $is_command = strpos($text,"/random");

            if($is_command !== false && $is_command === 0){
                $params = trim(str_replace("/random", "", $text));
                if (strlen($params) > 0) {
                    $params_array = explode(" ", $params);
                    foreach ($params_array as $value) {
                        if (!is_numeric($value)) {
                            $bot->sendMessage($id, "Ошибка в распознавании команды");
                            return;
                        }
                    }
                }

                switch (count($params_array)){
                    case 0:
                        $num = rand(0, 100);
                        $answer = 'Случайное число: ' . $num;
                        break;
                    case 1:
                        $num = rand (0, $params_array[0]);
                        $answer = 'Случайное число: ' . $num;
                        break;
                    case 2:
                        $num = rand ($params_array[0], $params_array[1]);
                        $answer = 'Случайное число: ' . $num;
                        break;
                    case 3:
                        $answer = "Случайные числа: ";
                        for ($i = 0; $i < $params_array[2]; $i++) {
                            $answer .= rand ($params_array[0], $params_array[1]) . " ";
                        }
                        break;
                    default:
                        $answer = "Слишком много параметров!";
                        break;
                }

                $bot->sendMessage($id, $answer);
            } elseif (text_analyse($text, "Подкинь монетку")) {
                $result = rand(0,1);
                if ($result) $answer = "Орел!";
                else $answer = "Решка!";

                $bot->sendMessage($id, $answer);
            } elseif (text_analyse($text, "Брось кубики")) {
                $answer = "Выпали кубики с числами " . rand(1,6) . " и " . rand(1,6);

                $bot->sendMessage($id, $answer);
            }
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

    /**
     * Set user language to interact with bot
     *
     * @param $bot
     * @return Closure
     */
    public function setLanguage($bot){
        return function ($message) use ($bot) {
            $telegram_id = $message->getChat()->getId();

            $language = MainController::checkLanguage($telegram_id);
            if (!is_null($language)) {
                $bot->sendMessage($telegram_id, "У вас уже установлен язык - $language");
            } else {
                $bot->sendMessage($telegram_id, "Язык еще не был установлен");
            }

            $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup([[
                                                                            ["text" => "Русский", "request_location" => true],
                                                                            ["text" => "English", "request_location" => true]
                                                                        ]], true, true);

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
    public static function checkLanguage($telegram_id){
        $language = UserModel::getUserLanguage($telegram_id);
        if (!empty($language)) {
            return $language;
        }

        return null;
    }
    
    public function returnTrue(){
        return function(){
            return true;
        };
    }
}