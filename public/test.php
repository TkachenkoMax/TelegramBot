<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 28.08.17
 * Time: 12:25
 */

include_once __ROOT__ . "/Components/Scheduler.php";

$scheduler = new Scheduler();
$scheduler->runTask();