<?php

namespace hesabro\helpers\widgets;

use hesabro\helpers\CKEditorFontAsset;
use skeeks\yii2\ckeditor\CKEditorPresets;
use skeeks\yii2\ckeditor\CKEditorWidget as BaseEditorWidget;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class CKEditorWidget extends BaseEditorWidget
{
    /**
     * Override clientOptions
     * 
     * @var array
     */
    public $clientOptions = [];

    /**
     * Override clientBaseOptions
     * 
     * @return array
     */
    private function clientBaseOptions()
    {
        return [
            'language' => 'fa',
            'font_names' => implode(';', array_map(fn($set) => implode('/', $set), [
                ['ایران‌سنس', 'IRANSans'],
                ['وزیر', 'vazir'],
                ['علی‌بابا', 'alibaba'],
                ['پیدا', 'peyda'],
                ['تیتر', 'titr'],
                ['رویا', 'roya'],
                ['نازنین', 'nazanin'],
                ['لوتوس', 'lotus'],
                ['نستعلیق', 'irannastaliq'],
            ])),
        ];
    }

    public function registerFonts() {
        $view = $this->getView();
        CKEditorFontAsset::register($view);
    }

    /**
     * Initializes the widget options.
     * This method sets the default values for various options.
     */
    protected function _initOptions()
    {
        $options = [];

        if ($this->preset) {
            $options = CKEditorPresets::getPresets($this->preset);
        }


        $this->clientOptions['contentsCss'] = Url::base() . '/fonts/bundle.css';
        $this->clientOptions = ArrayHelper::merge((array) $options, $this->clientBaseOptions(), $this->clientOptions);
    }

    public function run()
    {
        $this->registerFonts();
        parent::run();
    }
}
