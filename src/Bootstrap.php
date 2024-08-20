<?php

namespace hesabro\helpers;

use hesabro\helpers\components\Env;
use hesabro\helpers\components\Helper;
use hesabro\helpers\components\Jdf;
use hesabro\helpers\components\PhpNewVer;
use kartik\grid\Module as GridModule;
use yii\base\Application;
use yii\base\BootstrapInterface;

class Bootstrap implements BootstrapInterface
{
    /**
     * Bootstrap method to be called during application bootstrap stage.
     * @param Application $app the application currently running
     */
    public function bootstrap($app)
    {
        $app->params['bsVersion'] = 4;

        $app->setComponents([
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

        $app->setModules([
            'gridview' => [
                'class' => GridModule::class
            ]
        ]);
    }
}
