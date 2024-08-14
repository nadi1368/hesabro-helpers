<?php

use hesabro\helpers\components\Env;
use hesabro\helpers\components\Helper;
use hesabro\helpers\components\Jdf;
use hesabro\helpers\components\PhpNewVer;
use kartik\grid\Module as GridModule;

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
        ],
        'gridview' => [
            'class' => GridModule::class
        ]
    ]
];
