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
function test_file($update) {
    $file = "commands.txt";
    if (!file_exists($file)) {
        $fp = fopen($file, "rw");
        fwrite($fp, "Created file!\n\n");
        fclose($fp);
    }
    file_put_contents('commands.txt', print_r($update, 1), FILE_APPEND);
}

function text_analyse($incoming_text, $correct_text) {
    $similar = levenshtein(strtolower($incoming_text), $correct_text);
    if ($similar <= 10) return true;
    return false;
}