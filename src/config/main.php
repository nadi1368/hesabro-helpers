<?php

use hesabro\helpers\components\Helper;
use hesabro\helpers\components\Jdf;
use hesabro\helpers\components\PhpNewVer;
use hesabro\helpers\Module;
use yii\mongodb\Connection;
use yii\i18n\PhpMessageSource;

return [
    'components' => [
        'mongodb' => [
            'class' => Connection::class,
            'dsn' => Module::instance()->mongodbDsn
        ],
        'helper' => [
            'class' => Helper::class
        ],
        'phpNewVer' => [
            'class' => PhpNewVer::class
        ],
        'jdf' => [
            'class' => Jdf::class
        ],
        'i18n' => [
            'translations' => [
                'hesabro/helpers' => [
                    'class' => PhpMessageSource::class,
                    'basePath' => __DIR__ . '/../messages',
                    'fileMap' => [
                        'hesabro-helper' => 'module.php'
                    ]
                ]
            ]
        ],
    ]
];
