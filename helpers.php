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