<?php

namespace hesabro\helpers;

use Yii;
use yii\base\Module as BaseModule;

class Module extends BaseModule
{
    public function init(): void
    {
        parent::init();
    }

    public static function t($category, $message, $params = [], $language = null)
    {
        return Yii::t('hesabro/helpers/' . $category, $message, $params, $language);
    }
}