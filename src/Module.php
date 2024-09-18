<?php

namespace hesabro\helpers;

use hesabro\helpers\components\Env;
use hesabro\helpers\components\Helper;
use hesabro\helpers\components\Jdf;
use hesabro\helpers\components\PhpNewVer;
use kartik\grid\Module as GridModule;
use yii\base\Module as BaseModule;

class Module extends BaseModule
{
    public function init(): void
    {
        parent::init();

        $this->setComponents([
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
        ]);

        $this->setModules([
            'gridview' => [
                'class' => GridModule::class
            ]
        ]);
    }
}