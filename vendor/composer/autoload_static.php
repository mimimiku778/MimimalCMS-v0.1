<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitb99d262e1b8550ce1d6fd4bec6885e85
{
    public static $files = array (
        '1d0fe90f140113528a0ae23f10e7252d' => __DIR__ . '/../..' . '/core/ExceptionHandler.php',
        'c50be35c8e895cc991f499d8e5df3f9c' => __DIR__ . '/../..' . '/core/Kernel/MimimalCMS_Exceptions.php',
        '6e72f8d9ac0b701176965e247d9c2031' => __DIR__ . '/../..' . '/core/Kernel/MimimalCMS_API_Kernel_Config.php',
        '16472e4e181a8ef254f1473b8a678f2c' => __DIR__ . '/../..' . '/core/Kernel/MimimalCMS_API_Kernel_Interfaces.php',
        '5dbda25d14830118a5d46ac925c79592' => __DIR__ . '/../..' . '/core/Kernel/KernelInterfaces.php',
        '43e6048874fefc458cabf8e408f4491d' => __DIR__ . '/../..' . '/core/MimimalCMS_API_Core_Enums.php',
        'b4055c9d5ef0ffab2dca67f09cd24ac3' => __DIR__ . '/../..' . '/core/MimimalCMS_API_Core_Interfaces.php',
        '9c505ea57b20dd4128019a6ca09d3bae' => __DIR__ . '/../..' . '/core/MimimalCMS_API_HelperFunctions.php',
    );

    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Shadow\\Storage\\' => 15,
            'Shadow\\Kernel\\RouteClasses\\' => 27,
            'Shadow\\Kernel\\Dispatcher\\' => 25,
            'Shadow\\Kernel\\' => 14,
            'Shadow\\Config\\' => 14,
            'Shadow\\' => 7,
        ),
        'A' => 
        array (
            'App\\Views\\' => 10,
            'App\\Services\\' => 13,
            'App\\Models\\' => 11,
            'App\\Controllers\\' => 16,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Shadow\\Storage\\' => 
        array (
            0 => __DIR__ . '/../..' . '/core/Storage',
        ),
        'Shadow\\Kernel\\RouteClasses\\' => 
        array (
            0 => __DIR__ . '/../..' . '/core/Kernel/RouteClasses',
        ),
        'Shadow\\Kernel\\Dispatcher\\' => 
        array (
            0 => __DIR__ . '/../..' . '/core/Kernel/Dispatcher',
        ),
        'Shadow\\Kernel\\' => 
        array (
            0 => __DIR__ . '/../..' . '/core/Kernel',
        ),
        'Shadow\\Config\\' => 
        array (
            0 => __DIR__ . '/../..' . '/shared/Config',
        ),
        'Shadow\\' => 
        array (
            0 => __DIR__ . '/../..' . '/core',
        ),
        'App\\Views\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app/Views',
        ),
        'App\\Services\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app/Services',
        ),
        'App\\Models\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app/Models',
        ),
        'App\\Controllers\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app/Controllers',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitb99d262e1b8550ce1d6fd4bec6885e85::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitb99d262e1b8550ce1d6fd4bec6885e85::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitb99d262e1b8550ce1d6fd4bec6885e85::$classMap;

        }, null, ClassLoader::class);
    }
}
