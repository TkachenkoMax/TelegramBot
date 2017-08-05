<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 05.08.17
 * Time: 9:27
 */

$url = parse_url(getenv("CLEARDB_DATABASE_URL"));

/**
 * Returns an array of database parameters
 */

return array(
    "host" => $url["host"],
    "username" => $url["user"],
    "password" => $url["pass"],
    "dbname" => substr($url["path"], 1),
    "charset" => 'utf8',  
);