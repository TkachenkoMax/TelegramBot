<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 22.08.17
 * Time: 16:50
 */

use Cmfcmf\OpenWeatherMap;
use Cmfcmf\OpenWeatherMap\Exception as OWMException;

$lang = 'en';

$units = 'metric';

$owm = new OpenWeatherMap('89f361866c196cada5b38c69e5d96a9e');

try {
    $weather = $owm->getWeather('Berlin', $units, $lang);
} catch(OWMException $e) {
    echo 'OpenWeatherMap exception: ' . $e->getMessage() . ' (Code ' . $e->getCode() . ').';
} catch(\Exception $e) {
    echo 'General exception: ' . $e->getMessage() . ' (Code ' . $e->getCode() . ').';
}

echo "Погода на сейчас: " . $weather->temperature;