<?php
/**
 * katarinahelp_bot
 *
 * @author - Tkachenko Max
 */

/**
 * __ROOT__ is a constant which returns
 * server's root dir path
 */
define('__ROOT__', require_once('../rootpath.php'));
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);

/**
 * Launch autoloader
 */
$loader = require_once __ROOT__ . '/Components/Autoload.php';

//header('Content-Type: text/html; charset=utf-8');

/**
 * Launch Telegram Bot API
 */

require_once(__ROOT__ . "/vendor/autoload.php");

BotTest::sendMessage();


$file = "test.txt";
//если файла нету... тогда
if (!file_exists($file)) {
    $fp = fopen($file, "rw"); // ("r" - считывать "w" - создавать "a" - добовлять к тексту),мы создаем файл
    fwrite($fp, "Created test file!\n");
    fclose($fp);
}
// Открываем файл для получения существующего содержимого
$current = file_get_contents($file);
// Добавляем нового человека в файл
$current .= "Launched application at " . time() . "\n";
// Пишем содержимое обратно в файл
file_put_contents($file, $current);

/**
 * Start handle telegram's requests
 */

$router = new Router();
$router->handle();