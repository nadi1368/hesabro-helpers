<?php
class Yii extends \yii\BaseYii
{
    /**
     * @var BaseApplication
     */
    public static $app;
}

/**
 * @property hesabro\helpers\components\Helper $helper
 * @property hesabro\helpers\components\Env $env
 * @property hesabro\helpers\components\Jdf $jdf
 * @property hesabro\helpers\components\PhpNewVer $phpNewVer
 */
abstract class BaseApplication extends \yii\base\Application {}
