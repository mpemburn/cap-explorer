<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitfb4c4b83a92b62e0c4aed33d1c98240e
{
    public static $files = array (
        '3917c79c5052b270641b5a200963dbc2' => __DIR__ . '/..' . '/kint-php/kint/init.php',
    );

    public static $prefixLengthsPsr4 = array (
        'K' => 
        array (
            'Kint\\' => 5,
        ),
        'C' => 
        array (
            'CapExplorer\\' => 12,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Kint\\' => 
        array (
            0 => __DIR__ . '/..' . '/kint-php/kint/src',
        ),
        'CapExplorer\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitfb4c4b83a92b62e0c4aed33d1c98240e::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitfb4c4b83a92b62e0c4aed33d1c98240e::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitfb4c4b83a92b62e0c4aed33d1c98240e::$classMap;

        }, null, ClassLoader::class);
    }
}