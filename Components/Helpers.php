<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 16.08.17
 * Time: 10:39
 */

function test(){
    $file = "test.txt";
    //если файла нет, тогда
    if (!file_exists($file)) {
        $fp = fopen($file, "rw");
        fwrite($fp, "Created test file!\n");
        fclose($fp);
    }
    // Открываем файл для получения существующего содержимого
    $current = file_get_contents($file);
    // Добавляем информацию в файл
    $current .= "Helper-function works " . time() . "\n";
    // Пишем содержимое обратно в файл
    file_put_contents($file, $current);
}

function incoming_command($command_text) {
    $file = "commands.txt";
    if (!file_exists($file)) {
        $fp = fopen($file, "rw");
        fwrite($fp, "Created commands file!\n\n");
        fclose($fp);
    }
    file_put_contents('commands.txt', print_r($command_text, 1), FILE_APPEND);
}

function text_analyse($incoming_text, $correct_text) {
    $similar = levenshtein(strtolower($incoming_text), $correct_text);
    if ($similar <= 10) return true;
    return false;
}