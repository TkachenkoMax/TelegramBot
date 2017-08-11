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

/**
 * Start handle telegram's requests
 */

$router = new Router();
$router->handle();