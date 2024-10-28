<?php

namespace hesabro\helpers\widgets;

use diecoding\dropify\Dropify;
use Yii;

class FileInput extends Dropify
{
    public ?string $defaultFile = null;

    public $imgFileExtensions = [

        'jpeg',
        'jpg',
        'png',
        'svg',
        'webp'
    ];

    public function init()
    {
        $this->pluginOptions = [
            'defaultFile' => $this->defaultFile,
            'messages' => Yii::t('app', 'Dropify')
        ];

        parent::init();
    }
}
