<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitc77a30ca8d60fcd96a2049b576e55191
{
    public static $prefixesPsr0 = array (
        'F' => 
        array (
            'FFmpeg' => 
            array (
                0 => __DIR__ . '/..' . '/olaferlandsen/ffmpeg-php-class/src',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixesPsr0 = ComposerStaticInitc77a30ca8d60fcd96a2049b576e55191::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}
