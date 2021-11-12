<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitdf8b4a0a50b1b2ddfe61ae1293f8c57b
{
    public static $files = array (
        'a4a119a56e50fbb293281d9a48007e0e' => __DIR__ . '/..' . '/symfony/polyfill-php80/bootstrap.php',
    );

    public static $prefixLengthsPsr4 = array (
        'W' => 
        array (
            'WP_Prometheus_Metrics\\' => 22,
        ),
        'S' => 
        array (
            'Symfony\\Polyfill\\Php80\\' => 23,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'WP_Prometheus_Metrics\\' => 
        array (
            0 => __DIR__ . '/../..' . '/includes/classes',
        ),
        'Symfony\\Polyfill\\Php80\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-php80',
        ),
    );

    public static $classMap = array (
        'Attribute' => __DIR__ . '/..' . '/symfony/polyfill-php80/Resources/stubs/Attribute.php',
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'Stringable' => __DIR__ . '/..' . '/symfony/polyfill-php80/Resources/stubs/Stringable.php',
        'UnhandledMatchError' => __DIR__ . '/..' . '/symfony/polyfill-php80/Resources/stubs/UnhandledMatchError.php',
        'ValueError' => __DIR__ . '/..' . '/symfony/polyfill-php80/Resources/stubs/ValueError.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitdf8b4a0a50b1b2ddfe61ae1293f8c57b::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitdf8b4a0a50b1b2ddfe61ae1293f8c57b::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitdf8b4a0a50b1b2ddfe61ae1293f8c57b::$classMap;

        }, null, ClassLoader::class);
    }
}
