<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit30e37e4406280450bdcfd6fc092dd33b
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'Psr\\Container\\' => 14,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Psr\\Container\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/container/src',
        ),
    );

    public static $prefixesPsr0 = array (
        'P' => 
        array (
            'Pimple' => 
            array (
                0 => __DIR__ . '/..' . '/pimple/pimple/src',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit30e37e4406280450bdcfd6fc092dd33b::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit30e37e4406280450bdcfd6fc092dd33b::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit30e37e4406280450bdcfd6fc092dd33b::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}
