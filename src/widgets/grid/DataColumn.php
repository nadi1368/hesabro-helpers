<?php

namespace hesabro\helpers\widgets\grid;

use Closure;
use yii\helpers\Html;
use kartik\grid\DataColumn as KartikDataColumn;

class DataColumn extends KartikDataColumn
{
    public $arrayFooterOptions = [];
    public function renderFooterCell($key = 0): string
    {
        if($this->arrayFooterOptions && isset($this->arrayFooterOptions[$key])) {
            $footerRowOption = $this->arrayFooterOptions[$key];
        } else {
            $footerRowOption = $this->footerOptions;
        }
        return Html::tag('td', $this->renderFooterCellContent($key), $footerRowOption);
    }

    protected function renderFooterCellContent($key = 0): string
    {
        $footer = is_array($this->footer) && $this->footer ? $this->footer[$key] : (is_array($this->footer) ? '' : $this->footer);
        return $footer instanceof Closure ? call_user_func($footer) : ($footer !== null && \Yii::$app->phpNewVer->trim($footer) !== '' ? $footer : $this->grid->emptyCell);
    }
}