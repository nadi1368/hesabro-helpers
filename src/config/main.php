<?php

use hesabro\helpers\components\Env;
use hesabro\helpers\components\Helper;
use hesabro\helpers\components\Jdf;
use hesabro\helpers\components\PhpNewVer;

return [
    'components' => [
        'helper' => [
            'class' => Helper::class
        ],
        'phpNewVer' => [
            'class' => PhpNewVer::class
        ],
        'jdf' => [
            'class' => Jdf::class
        ],
        'env' => [
            'class' => Env::class
        ]
    ]
];
