<?php

namespace hesabro\helpers;

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

        $configs = require __DIR__ . '/config/main.php';
        $app->setComponents($configs['components'] ?? []);
    }
}
