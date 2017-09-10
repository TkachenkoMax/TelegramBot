<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 16.08.17
 * Time: 10:39
 */

/**
 * Print parameter to text file on the server
 * 
 * @param $update
 */
function testFile($update) {
    $file = "test.php";
    if (!file_exists($file)) {
        $fp = fopen($file, "rw");
        fwrite($fp, "Created file!\n\n");
        fclose($fp);
    }
    file_put_contents('test.php', print_r($update, 1), FILE_APPEND);
}

/**
 * Print parameter to text file with errors on the server
 *
 * @param $message
 */
function printError($message) {
    $file = "errors.txt";
    if (!file_exists($file)) {
        $fp = fopen($file, "rw");
        fwrite($fp, "Created file!\n\n");
        fclose($fp);
    }
    file_put_contents('errors.txt', print_r($message, 1));
}

/**
 * Analise is incoming text similar to correct text
 * 
 * @param $incoming_text
 * @param $correct_text
 * @return bool
 */
function textAnalyse($incoming_text, $correct_text) {
    $similar = levenshtein(strtolower($incoming_text), $correct_text);
    if ($similar <= 10) return true;
    return false;
}

/**
 * Check if date in string is valid
 * 
 * @param $date
 * @return bool
 */
function isDateValid($date) {

    if (!is_string($date)) {
        return false;
    }

    $stamp = strtotime($date);

    if (!is_numeric($stamp)) {
        return false;
    }

    if ( checkdate(date('m', $stamp), date('d', $stamp), date('Y', $stamp)) ) {
        return true;
    }
    return false;
}

/**
 * Get information about languages from config file
 *
 * @param $parameter
 * @param $info_type
 * @param null $need_to_find
 * @return null
 */
function getLanguageInfo($parameter, $info_type, $need_to_find = null){
    if ($parameter !== null) {
        $bot_config = include(__ROOT__ . "/Config/bot.php");
        $app_languages = $bot_config['available_languages'];

        foreach ($app_languages as $app_language) {
            if ($app_language[$info_type] == $parameter) return $app_language[$need_to_find];
        }
    }
    return null;
}

/**
 * Create text about weather
 *
 * @param array $params
 * @param $isDetailed
 * @return string
 */
function createWeatherText(array $params, $isDetailed){
    $text = "<b>Погода в " . $params["city"] . " (" . $params["country"] . ") на " . $params["date"] . "</b>" .
    "\n<b>Описание</b>: " . $params["description"] .
    "\n<b>Температура сейчас</b>: " . $params["temperature_now"] . " C" .
    "\n<b>Температура мин/макс</b>: " . $params["temperature_min"] . " C / " . $params["temperature_max"] . " C" .
    "\n<b>Осадки</b>: " . ($params["precipitation"] == "" ? "нет" : $params["precipitation"]);

    if ($isDetailed)
        $text .= "\n<b>Влажность</b>: " . $params["humidity"] .
        "\n<b>Давление</b>: " . $params["pressure"] .
        "\n<b>Скорость и направление ветра</b>: " . $params["wind_speed"] . ", " . $params["wind_direction"] .
        "\n<b>Восход солнца</b>: " . $params["sun_rise"] .
        "\n<b>Закат солнца</b>: " . $params["sun_set"];

    return $text;
}

/**
 * Download file from internal server to this server by file url
 *
 * @param $url
 * @return string
 */
function downloadFile($url, $id) {
    $path = __ROOT__ . "/public/files/instagram_images/instagram.jpg";

    $read_file = fopen($url, "rb");
    if ($read_file) {
        $write_file = fopen($path, "wb");
        if ($write_file){
            while(!feof($read_file)) {
                fwrite($write_file, fread($read_file, 4096));
            }
            fclose($write_file);
        }
        fclose($read_file);

        return "files/images/instagram.jpg";
    }
}

/**
 * Returns value from config file 'Config/bot.php'
 * 
 * @param $value
 * @return mixed
 */
function getValueFromConfig($value) {
    $config = include(__ROOT__ . "/Config/bot.php");
    return $config[$value];
}

/**
 * Get needed message in needed translation
 *
 * @param $message
 * @param $user
 * @param $parameters
 * @return string
 */
function translateMessage($message, $user, $parameters){
    $language = getUserLanguage($user);
    $username = getUserUsername($user);
    $needed_dictionary = explode(".", $message);

    $dictionary_name = $needed_dictionary[0];
    $dictionary_message = $needed_dictionary[1];

    $dictionary = include(__ROOT__ . "{$language}/{$dictionary_name}.php");

    $answer_with_placeholders = $dictionary["$dictionary_message"];

    if (is_array($answer_with_placeholders)) {
        $number_of_message = rand(0, count($answer_with_placeholders));
        $answer_with_placeholders = $answer_with_placeholders[$number_of_message];
    }

    $parameters["username"] = $username;

    $answer = fillPlaceholdersInMessage($answer_with_placeholders, $parameters);
    
    return $answer;
}

/**
 * Get user language for translations
 *
 * @param $user
 * @return string
 */
function getUserLanguage($user) {
    if ($user == null || $user->getTelegramLanguage() == null)
        return "en";

    $user_language = $user->getTelegramLanguage()->getLanguageName();
    $available_languages = getValueFromConfig("available_languages");

    foreach ($available_languages as $item) {
        if ($item["database_name"] == $user_language);
            return $item["translator"];
    }

    return "en";
}

/** Get user username (alias or first name and last name) for translations
 * @param $user
 * @return string
 */
function getUserUsername($user) {
    if ($user->getAlias() != "")
        return $user->getAlias();

    return $user->getFirstName() . " " . $user->getLastName();
}

/**
 * Create final view of a message, replacing placeholders with different values
 *
 * @param $answer
 * @param $parameters
 * @return mixed
 */
function fillPlaceholdersInMessage($answer, array $parameters) {
    foreach ($parameters as $key => $value){
        $placeholder = ":" . $key;
        $answer = str_replace($placeholder, $value, $answer);
    }

    return $answer;
}