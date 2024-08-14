<?php

namespace hesabro\helpers\widgets\DateRangePicker;

use yii\web\AssetBundle;

class DateRangePickerAssets extends AssetBundle
{
    public $sourcePath = '@hesabro/helpers/assets';

    public $css = [
        'css/daterangepicker.css',
        'css/datepicker-theme.css',
    ];

    public $js = [
        'js/moment.min.js',
        'js/moment-jalaali.js',
        'js/daterangepicker-fa-ex.js',
    ];

    public $depends = [
        'yii\bootstrap4\BootstrapPluginAsset',
    ];
}