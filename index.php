<?php
/**
 * katarinahelp_bot
 *
 * @author - Tkachenko Max
 */
header('Content-Type: text/html; charset=utf-8');
// подрубаем API
require_once("vendor/autoload.php");

// создаем переменную бота
$token = "390875011:AAEkDpRCBeIiRQ3clQ03PcEDty5QzEwtb60";
$bot = new \TelegramBot\Api\Client($token);

// если бот еще не зарегистрирован - регистрируем
if(!file_exists("registered.trigger")){ 
	/**
	 * файл registered.trigger будет создаваться после регистрации бота. 
	 * если этого файла нет значит бот не зарегистрирован 
	 */
	 
	// URl текущей страницы
	$page_url = "https://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	$result = $bot->setWebhook($page_url);
	if($result){
		file_put_contents("registered.trigger",time()); // создаем файл дабы прекратить повторные регистрации
	}
}

$output = json_decode(file_get_contents('php://input'), true);
file_put_contents('logs.txt', PHP_EOL . $output, FILE_APPEND);


// обязательное. Запуск бота
$bot->command('start', function ($message) use ($bot) {
	//$datas = json_decode(file_get_contents("php://input"),true);
	//file_put_contents('logs.txt', PHP_EOL . $datas, FILE_APPEND);
	$answer = 'Добро пожаловать!';
    $bot->sendMessage($message->getChat()->getId(), $answer);
});

// помощ
$bot->command('help', function ($message) use ($bot) {
    $answer = 'Команды:
/help - помощ';
    $bot->sendMessage($message->getChat()->getId(), $answer);
});

// запускаем обработку
$bot->run();
?>