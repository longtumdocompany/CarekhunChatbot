<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitd594c4af506a28c1cae652fdd118688b
{
    public static $prefixLengthsPsr4 = array (
        'L' => 
        array (
            'LINE\\' => 5,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'LINE\\' => 
        array (
            0 => __DIR__ . '/..' . '/linecorp/line-bot-sdk/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitd594c4af506a28c1cae652fdd118688b::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitd594c4af506a28c1cae652fdd118688b::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}