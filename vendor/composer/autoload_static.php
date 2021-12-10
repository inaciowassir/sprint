<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit1e6bce91c2b5e0f1c60f58e70682ca9a
{
    public static $files = array (
        '320cde22f66dd4f5d3fd621d3e88b98f' => __DIR__ . '/..' . '/symfony/polyfill-ctype/bootstrap.php',
        '864a922daa0483798f20c1257f4d6754' => __DIR__ . '/../..' . '/app/helpers/Exposer.php',
    );

    public static $prefixLengthsPsr4 = array (
        's' => 
        array (
            'sprint\\' => 7,
        ),
        'S' => 
        array (
            'Symfony\\Polyfill\\Ctype\\' => 23,
        ),
        'P' => 
        array (
            'PhpOption\\' => 10,
            'PHPMailer\\PHPMailer\\' => 20,
        ),
        'F' => 
        array (
            'Faker\\' => 6,
        ),
        'D' => 
        array (
            'Dotenv\\' => 7,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'sprint\\' => 
        array (
            0 => __DIR__ . '/../..' . '/',
        ),
        'Symfony\\Polyfill\\Ctype\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-ctype',
        ),
        'PhpOption\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpoption/phpoption/src/PhpOption',
        ),
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
        'Faker\\' => 
        array (
            0 => __DIR__ . '/..' . '/fzaninotto/faker/src/Faker',
        ),
        'Dotenv\\' => 
        array (
            0 => __DIR__ . '/..' . '/vlucas/phpdotenv/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit1e6bce91c2b5e0f1c60f58e70682ca9a::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit1e6bce91c2b5e0f1c60f58e70682ca9a::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit1e6bce91c2b5e0f1c60f58e70682ca9a::$classMap;

        }, null, ClassLoader::class);
    }
}