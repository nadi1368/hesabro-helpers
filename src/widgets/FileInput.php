<?php

namespace hesabro\helpers\widgets;

use diecoding\dropify\Dropify;
use hesabro\helpers\Module;

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
            'messages' => [
                'default' => Module::t('module', 'Dropify Default'),
                'replace' => Module::t('module', 'Dropify Replace'),
                'remove' => Module::t('module', 'Remove'),
                'error' => Module::t('module', 'Dropify Error'),
            ]
        ];

        parent::init();
    }
}
