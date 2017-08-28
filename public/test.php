<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 28.08.17
 * Time: 12:25
 */

define('__ROOT__', require_once('../rootpath.php'));
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);

/**
 * Launch autoloader
 */
$loader = require_once __ROOT__ . '/Components/Autoload.php';

/**
 * Launch Telegram Bot API
 */

require_once(__ROOT__ . "/vendor/autoload.php");

$scheduler = new Scheduler();
$scheduler->runTask();