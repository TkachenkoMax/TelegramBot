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
    $file = "test.txt";
    if (!file_exists($file)) {
        $fp = fopen($file, "rw");
        fwrite($fp, "Created file!\n\n");
        fclose($fp);
    }
    file_put_contents('test.txt', print_r($update, 1), FILE_APPEND);
}

/**
 * Analise is incoming text similar to correct text
 * 
 * @param $incoming_text
 * @param $correct_text
 * @return bool
 */
function text_analyse($incoming_text, $correct_text) {
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