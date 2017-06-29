<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit8f856caba64f36701f23c73f1c2f0d7b
{
    public static $prefixLengthsPsr4 = array (
        'T' => 
        array (
            'TelegramBot\\Api\\' => 16,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'TelegramBot\\Api\\' => 
        array (
            0 => __DIR__ . '/..' . '/telegram-bot/api/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit8f856caba64f36701f23c73f1c2f0d7b::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit8f856caba64f36701f23c73f1c2f0d7b::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}