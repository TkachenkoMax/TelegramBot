<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 22.08.17
 * Time: 16:50
 */

use \Yandex\Geo\Api;

$api = new Api();

// Или можно икать по адресу
$api->setQuery('Павловка');

// Настройка фильтров
$api
    ->setLimit(10) // кол-во результатов
    ->setLang(Api::LANG_RU) // локаль ответа
    ->load();

$response = $api->getResponse();

// Список найденных точек
$collection = $response->getList();
foreach ($collection as $item) {
    $item->getAddress(); // вернет адрес
    $item->getLatitude(); // широта
    $item->getLongitude(); // долгота
    $item->getData(); // необработанные данные
}