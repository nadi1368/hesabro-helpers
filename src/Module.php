<?php

namespace hesabro\helpers;

use Yii;
use yii\base\Module as BaseModule;

class Module extends BaseModule
{
    public mixed $mongodbDsn = null;

    public mixed $user = null;

    public array $ignoreClasses = [];

    public function init(): void
    {
        parent::init();

        Yii::configure($this, require __DIR__ . '/config/main.php');
    }
}