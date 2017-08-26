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
    file_put_contents('test.txt', print_r($message, 1));
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
    $text = "Погода в " . $params["city"] . " (" . $params["country"] . ") на " . $params["date"] .
    "\nОписание: " . $params["description"] .
    "\nТемпература сейчас: " . $params["temperature_now"] . " C" .
    "\nТемпература мин/макс: " . $params["temperature_min"] . " C / " . $params["temperature_max"] . " C" .
    "\nОсадки: " . ($params["precipitation"] == "" ? "нет" : $params["precipitation"]);

    if ($isDetailed)
        $text .= "\nВлажность: " . $params["humidity"] .
        "\nДавление: " . $params["pressure"] .
        "\nСкорость и направление ветра: " . $params["wind_speed"] . ", " . $params["wind_direction"] .
        "\nВосход солнца: " . $params["sun_rise"] .
        "\nЗакат солнца: " . $params["sun_set"];

    return $text;
}