<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit24c317dbe22e12097f887d8fab7d353b
{
    public static $prefixLengthsPsr4 = array (
        'a' => 
        array (
            'allinpay\\' => 9,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'allinpay\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit24c317dbe22e12097f887d8fab7d353b::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit24c317dbe22e12097f887d8fab7d353b::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
