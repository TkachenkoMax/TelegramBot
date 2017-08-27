<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit8f856caba64f36701f23c73f1c2f0d7b
{
    public static $files = array (
        'c964ee0ededf28c96ebd9db5099ef910' => __DIR__ . '/..' . '/guzzlehttp/promises/src/functions_include.php',
        'a0edc8309cc5e1d60e3047b5df6b7052' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/functions_include.php',
        '37a3dc5111fe8f707ab4c132ef1dbc62' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/functions_include.php',
        '5e8e37dc61dd0e133a92c2f8f54c8544' => __DIR__ . '/../..' . '/Components/Helpers.php',
    );

    public static $prefixLengthsPsr4 = array (
        'T' => 
        array (
            'TelegramBot\\Api\\' => 16,
        ),
        'P' => 
        array (
            'Psr\\Http\\Message\\' => 17,
        ),
        'M' => 
        array (
            'MenaraSolutions\\Geographer\\Helpers\\' => 35,
            'MenaraSolutions\\Geographer\\' => 27,
        ),
        'I' => 
        array (
            'InstagramAPI\\' => 13,
        ),
        'G' => 
        array (
            'GuzzleHttp\\Psr7\\' => 16,
            'GuzzleHttp\\Promise\\' => 19,
            'GuzzleHttp\\' => 11,
        ),
        'C' => 
        array (
            'Cmfcmf\\' => 7,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'TelegramBot\\Api\\' => 
        array (
            0 => __DIR__ . '/..' . '/telegram-bot/api/src',
        ),
        'Psr\\Http\\Message\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/http-message/src',
        ),
        'MenaraSolutions\\Geographer\\Helpers\\' => 
        array (
            0 => __DIR__ . '/..' . '/menarasolutions/geographer-data/helpers',
        ),
        'MenaraSolutions\\Geographer\\' => 
        array (
            0 => __DIR__ . '/..' . '/menarasolutions/geographer/src',
        ),
        'InstagramAPI\\' => 
        array (
            0 => __DIR__ . '/..' . '/mgp25/instagram-php/src',
        ),
        'GuzzleHttp\\Psr7\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/psr7/src',
        ),
        'GuzzleHttp\\Promise\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/promises/src',
        ),
        'GuzzleHttp\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/guzzle/src',
        ),
        'Cmfcmf\\' => 
        array (
            0 => __DIR__ . '/..' . '/cmfcmf/openweathermap-php-api/Cmfcmf',
        ),
    );

    public static $prefixesPsr0 = array (
        'Y' => 
        array (
            'Yandex\\Geo' => 
            array (
                0 => __DIR__ . '/..' . '/yandex/geo/source',
            ),
        ),
        'J' => 
        array (
            'JsonMapper' => 
            array (
                0 => __DIR__ . '/..' . '/netresearch/jsonmapper/src',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit8f856caba64f36701f23c73f1c2f0d7b::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit8f856caba64f36701f23c73f1c2f0d7b::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit8f856caba64f36701f23c73f1c2f0d7b::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}
