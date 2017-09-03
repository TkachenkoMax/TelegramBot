<?php

/**
 * Created by PhpStorm.
 * User: max
 * Date: 05.08.17
 * Time: 10:34
 */

use \Yandex\Geo\Api;
use Cmfcmf\OpenWeatherMap;
use Cmfcmf\OpenWeatherMap\Exception as OWMException;
use \InstagramAPI\Instagram;

class MainController
{
    /**
     * Register new user
     * 
     * @param $bot
     * @param User $user
     * @return Closure
     */
    public function register($bot, $user){
        return function ($message) use ($bot, $user) {
            if($user === null) {
                $telegram_id = $message->getFrom()->getId();
                $first_name = $message->getFrom()->getFirstName();
                $last_name = $message->getFrom()->getLastName();

                try {
                    UserModel::register($telegram_id, $first_name, $last_name);
                } catch (Exception $ex) {
                    if ($ex->getMessage() == "Bad registration") {
                        $bot->sendMessage($telegram_id, "Bad registration");
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
                $bot->sendMessage($telegram_id, $answer, "HTML");
            } else {
                $bot->sendMessage($user->getTelegramId(), "Вы уже зарегистрированы!");
            }
        };
    }

    /**
     * Calculate random number and other random functions
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
            } elseif (textAnalyse($text, "Подкинь монетку")) {
                $result = rand(0,1);
                if ($result) $answer = "Орел!";
                else $answer = "Решка!";

                $bot->sendMessage($id, $answer);
            } elseif (textAnalyse($text, "Брось кубики")) {
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
     * Set user's language to interact with bot
     *
     * @param $bot
     * @param User $user
     * @return Closure
     */
    public function setLanguage($bot, User $user){
        return function ($message) use ($bot, $user) {
            $text = trim($message->getText());
            $telegram_id = $user->getTelegramId();
            $language = $user->getTelegramLanguage();

            $params = trim(str_replace("/setLanguage", "", $text));
            if (strlen($params) > 0) {
                $parameter = strtolower(explode(" ", $params, 2)[0]);

                $lang_id = getLanguageInfo($parameter, "database_name", "database_id");
                    
                if($lang_id !== null){
                    UserModel::setUserLanguage($telegram_id, $lang_id);
                    $bot->sendMessage($telegram_id, "Язык $parameter установлен");
                } else {
                    $bot->sendMessage($telegram_id, "Нельзя выбрать этот язык");
                }
            }
            else {
                if (is_object($language)) {
                    $language_name = $language->getLanguageName();
                    $bot->sendMessage($telegram_id, "У вас уже установлен язык - $language_name");
                } else {
                    $bot->sendMessage($telegram_id, "Язык еще не был установлен");
                }

                $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup([[
                    ["text" => "/setLanguage русский"],
                    ["text" => "/setLanguage english"]
                ]], true, true);

                $bot->sendMessage($user->getTelegramId(), "Выберите язык:", false, null,null, $keyboard);
            }
        };
    }

    /**
     * Set user's alias
     *
     * @param $bot
     * @param User $user
     * @return Closure
     */
    public function setAlias($bot, User $user){
        return function ($message) use ($bot, $user) {
            $text = trim($message->getText());
            $telegram_id = $user->getTelegramId();
            
            $parameter = trim(str_replace("/setAlias", "", $text));
            if (strlen($parameter) > 0) {
                UserModel::setUserAlias($telegram_id, $parameter);

                $bot->sendMessage($telegram_id, "Псевдоним '$parameter' установлен!");
            } else {
                $bot->sendMessage($telegram_id, "Напиши какой надо установить псевдомним (/setAlias <псевдоним>)");
            }
        };
    }

    /**
     * Set user's date of birth
     * 
     * @param $bot
     * @param User $user
     * @return Closure
     */
    public function setDateOfBirth($bot, User $user){
        return function ($message) use ($bot, $user) {
            $text = trim($message->getText());
            $telegram_id = $user->getTelegramId();
            
            $parameter = trim(str_replace("/setDateOfBirth", "", $text));
            if (strlen($parameter) > 0) {
                if (isDateValid($parameter)) 
                    $date = new DateTime($parameter);
                else {
                    $bot->sendMessage($telegram_id, "Слишком сложно понять эту дату - $parameter");
                    return;
                }

                UserModel::setUserDateOfBirth($telegram_id, $date->format("Y-m-d H:i:s"));

                $bot->sendMessage($telegram_id, "Дата рождения $parameter установлена!");
            } else {
                $bot->sendMessage($telegram_id, "Не могу угадать твой день рождения, напиши его (/setDateOfBirth <дата>)");
            }
        };
    }

    /**
     * Set user's city
     *
     * @param $bot
     * @param User $user
     * @return Closure
     */
    public function setCity($bot, User $user){
        return function ($update) use ($bot, $user) {
            $message = $update->getMessage();
            $text = trim($message->getText());
            $location = $message->getLocation();
            $telegram_id = $user->getTelegramId();
            $current_city = $user->getCity();
            
            if (is_object($location)) {
                $long = $location->getLongitude();
                $lat = $location->getLatitude();
                $api = new Api();
                $api->setPoint($long, $lat);
                if ($user->getTelegramLanguage() != null)
                    $lang = getLanguageInfo($user->getTelegramLanguage()->getId(), "database_id", "yandex_geocoding");
                $lang = ($lang==null) ? $lang = "en-US" : $lang;

                $api
                    ->setLimit(1)
                    ->setLang($lang)
                    ->load();
                $response = $api->getResponse();
                $collection = $response->getList();

                $new_city = new City($collection[0]->getLocalityName(), $collection[0]->getCountry(), $collection[0]->getLongitude(), $collection[0]->getLatitude());

                UserModel::setCity($telegram_id, $new_city);

                $bot->sendMessage($telegram_id, "Город установлен: " . $collection[0]->getLocalityName());
            }

            $is_command = strpos($text,"/setCity");
            if($is_command !== false && $is_command === 0){
                if (is_object($current_city)) {
                    $city_name = $current_city->getCity();
                    $bot->sendMessage($telegram_id, "У вас уже установлен город - $city_name");
                } else {
                    $bot->sendMessage($telegram_id, "Город еще не был установлен");
                }
                $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup([[
                    ["text" => "Отправить местоположение", "request_location" => true],
                ]], true, true);
                $bot->sendMessage($user->getTelegramId(), "Нажмите на кнопку для определения города и разрешите отправку местоположения", false, null,null, $keyboard);
            }
        };
    }

    /**
     * Get weather forecast
     *
     * @param $bot
     * @param User $user
     * @return Closure
     */
    public function weather($bot, User $user){
        return function ($message) use ($bot, $user){
            $city = $user->getCity()->getCity();;
            $days = 1;
            $details = false;

            $params = str_replace("/weather", "", $message->getText());
            $arr = explode(",", trim($params));

            foreach ($arr as $element) {
                $element = trim($element);

                if (strpos($element, "город - ") === 0) {
                    $city = str_replace("город - ", "", $element);
                } elseif (strpos($element, "дни - ") === 0) {
                    $days = str_replace("дни - ", "", $element);
                    if ($days > 16 && $days < 1) {
                        $bot->sendMessage($user->getTelegramId(), "Укажите дни в диапазоне от 1 до 16");
                        return;
                    }
                } elseif (strpos($element, "подробно") === 0) {
                    $details = true;
                }
            }

            if ($city === null) {
                $bot->sendMessage($user->getTelegramId(), "Не указан город поиска");
                return;
            }

            $lang = "en";
            if ($user->getTelegramLanguage() != null) 
                $lang = getLanguageInfo($user->getTelegramLanguage()->getId(), "database_id", "forecast");
            $units = 'metric';

            $owm = new OpenWeatherMap('89f361866c196cada5b38c69e5d96a9e');

            try {
                $weather = $owm->getDailyWeatherForecast($city, $units, $lang, "", $days);
            } catch(OWMException $e) {
                printError('OpenWeatherMap exception: ' . $e->getMessage() . ' (Code ' . $e->getCode() . ').');
            } catch(\Exception $e) {
                printError('General exception: ' . $e->getMessage() . ' (Code ' . $e->getCode() . ').');
            }
            $globalText = "";

            foreach ($weather as $day_weather) {
                $params = array(
                    "date" => $day_weather->time->day->format('d.m.Y'),
                    "city" => $day_weather->city->name,
                    "country" => $day_weather->city->country,
                    "description" => $day_weather->weather->description,
                    "temperature_now" => $day_weather->temperature->now->getValue(),
                    "temperature_min" => $day_weather->temperature->min->getValue(),
                    "temperature_max" => $day_weather->temperature->max->getValue(),
                    "precipitation" => $day_weather->precipitation->getDescription(),
                    "humidity" => $day_weather->humidity->getFormatted(),
                    "pressure" => $day_weather->pressure->getFormatted(),
                    "wind_speed" => $day_weather->wind->speed->getFormatted(),
                    "wind_direction" => $day_weather->wind->direction->getFormatted(),
                    "sun_rise" => $day_weather->sun->rise->format("H:i:s"),
                    "sun_set" => $day_weather->sun->set->format("H:i:s"),
                );

                $globalText .= createWeatherText($params, $details);

                if(next($weather)) {
                    $globalText .= "\n\n";
                }
            }
            $bot->sendMessage($user->getTelegramId(), $globalText, "HTML");
        };
    }

    /**
     * Set Instagram account login
     *
     * @param $bot
     * @param User $user
     * @return Closure
     */
    public function instagramSetLogin($bot, User $user) {
        return function ($message) use ($bot, $user) {
            $parameter = trim(str_replace("/instagramLogin", "", $message->getText()));

            if ($parameter == "") {
                $bot->sendMessage($user->getTelegramId(), "Вы не ввели логин");
                return;
            }

            InstagramModel::setLogin($parameter, $user->getId());

            $bot->sendMessage($user->getTelegramId(), "Логин Instagram $parameter успешно установлен!");
        };
    }

    /**
     * Set Instagram account password
     *
     * @param $bot
     * @param User $user
     * @return Closure
     */
    public function instagramSetPassword($bot, User $user) {
        return function ($message) use ($bot, $user) {
            $parameter = str_replace("/instagramPassword", "", $message->getText());

            if (trim($parameter) == "") {
                $bot->sendMessage($user->getTelegramId(), "Вы не ввели пароль");
                return;
            }

            InstagramModel::setPassword($parameter, $user->getId());

            $bot->sendMessage($user->getTelegramId(), "Пароль Instagram успешно установлен!");
        };
    }

    private function instagramLogin($bot, User $user) {
        $ig = new Instagram();
        $instagram_account = InstagramModel::getByUserId($user->getId());

        try {
            $ig->setUser($instagram_account->getLogin(), $instagram_account->getPassword());
            $loginResponse = $ig->login();

            /*if (!is_null($loginResponse) && $loginResponse->getTwoFactorRequired()) {
                $twoFactorIdentifier = $loginResponse->getTwoFactorInfo()->getTwoFactorIdentifier();
                $verificationCode = trim(fgets(STDIN));
                $ig->twoFactorLogin($verificationCode, $twoFactorIdentifier);
                $bot->sendMessage($user->getTelegramId(), "Нужна двухфакторная авторизация");
            }*/
        } catch (\Exception $e) {
            $bot->sendMessage($user->getTelegramId(), "Something went wrong: " . $e->getMessage());
        }

        return $ig;
    }

    public function instagramTimeline($bot, User $user) {
        $ig = $this->instagramLogin($bot, $user);

        return function ($message) use ($bot, $user, $ig) {
            $number_of_photos = trim(str_replace("/instagramTimeline", "", $message->getText()));
            if (!is_numeric($number_of_photos) || $number_of_photos < 1 || $number_of_photos > 10) {
                $bot->sendMessage($user->getTelegramId(), "Введите число в пределах от 1 до 10");
                return;
            }

            $timeline = $ig->getTimelineFeed();

            $num_results = $timeline->getNumResults();

            if ($num_results < 7)
                $number_of_photos = $num_results;

            $feed = $timeline->getFeedItems();

            unset($feed[2]);

            $globalText = "";

            for ($i = 0; $i < $number_of_photos; $i++) {
                $globalText .= "i: $i , number_of_photos $number_of_photos ";

                if ($feed[$i] != null) {
                    if ($i == count($feed)+1) {
                        $timeline = $ig->getTimelineFeed($timeline->getNextMaxId());
                        $feed = $timeline->getFeedItems();
                        unset($feed[2]);
                        $number_of_photos -= $i;
                        $i = 0;

                        $globalText .= " Зашло в подгрузку";
                    }

                    $caption = "Описание: " . $feed[$i]->getCaption()->text .
                        "\n\nПользователь: " . $feed[$i]->getUser()->username . " (" . $feed[$i]->getUser()->full_name . ")" .
                        "\n\nID поста: " . $feed[$i] ->getId();

                    switch ($feed[$i]->getMediaType()) {
                        case 1:
                            $photo = $feed[$i]->getImageVersions2();
                            $photo_url = $photo->candidates[0]->url;
                            
                            $bot->sendPhoto($user->getTelegramId(), $photo_url, $caption);
                            $bot->sendMessage($user->getTelegramId(), "Open in Instagram: " . $feed[$i]->getItemUrl(), null, true);
                            break;
                        case 2:
                            $video = $feed[$i]->getVideoVersions();
                            $video_url = $video[0]->url;

                            $bot->sendVideo($user->getTelegramId(), $video_url, null, $caption);
                            $bot->sendMessage($user->getTelegramId(), "Open in Instagram: " . $feed[$i]->getItemUrl(), null, true);
                            break;
                        case 8:
                            $carousel_media = $feed[$i]->getCarouselMedia();
                            foreach ($carousel_media as $media) {
                                switch ($media->media_type) {
                                    case 1:
                                        $photo_url = $media->image_versions2->candidates[0]->url;

                                        $bot->sendPhoto($user->getTelegramId(), $photo_url, $caption);
                                        break;
                                    case 2:
                                        $video_url = $media->video_version->url;
                                        $bot->sendVideo($user->getTelegramId(), $video_url, null, $caption);
                                        break;
                                }
                            }
                            $bot->sendMessage($user->getTelegramId(), "Open in Instagram: " . $feed[$i]->getItemUrl(), null, true);
                            break;
                    }
                } else
                    $number_of_photos++;
            }
            testFile($globalText);
        };
    }

    public function instagramPostPhoto($bot, User $user){
        $ig = $this->instagramLogin($bot, $user);

        return function ($message) use ($bot, $user, $ig) {
            $timeline = $ig->getTimelineFeed();
            //testFile($timeline);

            /*$metadata = [
                'caption' => 'My awesome photo uploaded with telegram bot, fuck yeah',
            ];

            $ig->uploadTimelinePhoto("files/photo.jpg", $metadata);*/

            /*$userId = $ig->getUsernameId('acc_for_testing_api');
            $a = $ig->getUserInfoById($userId);

            $users = $ig->getAutoCompleteUserList();
            testFile($users);
            
            $timeline = $ig->getTimelineFeed();
            testFile($timeline);          
            
            $activity = $ig->getRecentActivity();
            testFile($activity);
            
            $info = $ig->getUserInfoByName("kurshakova_katerina");
            testFile($info);
            
            $followings = $ig->getUserFollowings($ig->getUsernameId("kurshakova_katerina"));
            testFile($followings);
            
            $followers = $ig->getUserFollowers($ig->getUsernameId("kurshakova_katerina"));
            testFile($followers);
            
            
            $username = $a->getUser()->getUsername();
            $full_name = $a->user->getFullName();

            $bot->sendMessage($user->getTelegramId(), "Username: " . $username . ", full name: " . $full_name);*/

            $bot->sendMessage($user->getTelegramId(), "Успех!");
        };
    }

    /**
     * Send congratulations about birthdays to users
     * 
     * @param $bot
     * @throws Exception
     */
    public function congratsToUsers($bot){
        $today = new DateTime("now");

        $birthday_users = UserModel::getUsersWithBirthdayToday($today);

        if ($birthday_users !== null) {
            foreach ($birthday_users as $user) {
                $bot->sendMessage($user->getTelegramId(), "Поздравляю тебя с днем рождения!");
            }
        }
    }

    /**
     * Sent to user message with information about him
     *
     * @param $bot
     * @param User $user
     * @return Closure
     */
    public function aboutMe($bot, User $user) {
        return function () use ($bot, $user) {
            $message = "<b>Информация о Вас</b>" .
            "\n<b>Имя и фамилия</b> : " . $user->getFirstName() . " " . $user->getLastName() .
            "\n<b>Дата рождения</b>: " . ( is_object($user->getDateOfBirth()) ? $user->getDateOfBirth()->format("d-m-Y") : "не установлена" ) .
            "\n<b>Город</b>: " . ( is_object($user->getCity()) ? $user->getCity()->getCity() : "не установлен" ) .
            "\n<b>Язык</b>: " . ( is_object($user->getTelegramLanguage()) ? $user->getTelegramLanguage()->getLanguageName() : "не установлен" ) .
            "\n<b>Псевдоним</b>: " . ( $user->getAlias() !== null ? $user->getAlias() : "не установлен" );

            $bot->sendMessage($user->getTelegramId(), $message, "HTML");
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