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
     * @param User $user
     * @return Closure
     */
    public function register($bot, User $user){
        return function () use ($bot, $user) {
            if(!is_null($user)) {
                $telegram_id = $user->getTelegramId();
                $first_name = $user->getFirstName();
                $last_name = $user->getLastName();

                try {
                    UserModel::register($telegram_id, $first_name, $last_name);
                } catch (Exception $ex) {
                    if ($ex->getMessage() == "Bad registration") {
                        $bot->sendMessage($user->getTelegramId(), "Bad registration");
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
                $bot->sendMessage($user->getTelegramId(), $answer, "HTML");
            } else {
                $bot->sendMessage($user->getTelegramId(), "Вы уже зарегистрированы!");
            }
        };
    }

    /**
     * Calculate random number
     * 
     * @param $bot
     * @param User $user
     * @return Closure
     */
    public function random($bot, User $user){
        return function($update) use ($bot, $user){
            $message = $update->getMessage();
            $text = trim($message->getText());
            $id = $user->getTelegramId();

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
     * @param User $user
     * @return Closure
     */
    public function showHelp($bot, User $user){
        return function () use ($bot, $user) {
            $answer = 'Команды:
/help - помощь
/random - сгенерировать случайное число от 0 до 100';
            $bot->sendMessage($user->getTelegramId(), $answer);
        };
    }

    /**
     * Set user language to interact with bot
     *
     * @param $bot
     * @param User $user
     * @param array $app_languages
     * @return Closure
     */
    public function setLanguage($bot, User $user, array $app_languages){
        return function ($update) use ($bot, $user, $app_languages) {
            $message = $update->getMessage();
            $text = trim($message->getText());
            $telegram_id = $user->getTelegramId();
            $language = $user->getTelegramLanguage();

            $is_command = strpos($text,"/setLanguage");

            if($is_command !== false && $is_command === 0){
                $params = trim(str_replace("/setLanguage", "", $text));
                if (strlen($params) > 0) {
                    $param_arr = explode(" ", $params);
                    $parameter = strtolower($param_arr[0]);

                    $bot->sendMessage($telegram_id, $parameter);

                    if(in_array($parameter, array_flip($app_languages))){
                        UserModel::setUserLanguage($telegram_id, $app_languages[$parameter]);
                        $bot->sendMessage($telegram_id, "Язык $parameter установлен");
                    } else {
                        $bot->sendMessage($telegram_id, "Нельзя выбрать этот язык");
                    }
                }
                else {
                    if (!is_null($language)) {
                        $bot->sendMessage($telegram_id, "У вас уже установлен язык - $language");
                    } else {
                        $bot->sendMessage($telegram_id, "Язык еще не был установлен");
                    }

                    $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup([[
                        ["text" => "/setLanguage русский"],
                        ["text" => "/setLanguage english"]
                    ]], true, true);

                    $bot->sendMessage($user->getTelegramId(), "Выберите язык:", false, null,null, $keyboard);
                }
            }
        };
    }

    /**
     * Function that we need to use "on" function of the library
     *
     * @return Closure
     */
    public function returnTrue(){
        return function(){
            return true;
        };
    }
}