<?php

namespace hesabro\helpers\widgets;

use hesabro\helpers\Module;
use kartik\select2\Select2;
use yii\helpers\Url;
use yii\web\JsExpression;

class UserSelect2 extends Select2
{
    public string $relation = 'user';

    public string $fullNameAttribute = 'fullName';

    public function init()
    {
        parent::init();
    }

    private function setInitValueText()
    {
        $this->initValueText = $this->model->{$this->attribute} ? $this->model->{$this->relation}?->{$this->fullNameAttribute} : '';
    }

    private function setOptions()
    {
        $this->options = [
            'placeholder' => Module::t('module', 'Search'),
            'dir' => 'rtl'
        ];
    }

    private function setPluginOptions()
    {
        $this->pluginOptions = [
            'allowClear' => true,
            'minimumInputLength' => 3,
            'language' => [
                'errorLoading' => new JsExpression("function () { return '" . Module::t('module', 'Error In Loading') . "'; }"),
                'inputTooShort' => new JsExpression("function () { return '" . Module::t('module', 'Type Something Please') . "'; }"),
                'loadingMore' => new JsExpression("function () { return '" . Module::t('module', 'Loading More') . "'; }"),
                'noResults' => new JsExpression("function () { return '" . Module::t('module', 'No items found') . "'; }"),
                'searching' => new JsExpression("function () { return '" . Module::t('module', 'Searching') . "'; }"),
                'maximumSelected' => new JsExpression("function () { return '" . Module::t('module', 'Maximum Selected') . "'; }"),
            ],
            'ajax' => [
                'url' => Url::to(Module::getInstance()->userFindUrl),
                'dataType' => 'json',
                'data' => new JsExpression('function(params) { return {q:params.term}; }')
            ],
            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
            'templateResult' => new JsExpression('function(data) { return data.text; }'),
            'templateSelection' => new JsExpression('function (data) { return data.text; }'),
        ];
    }
}