<?php

namespace hesabro\helpers\widgets;

use yii\widgets\ListView as YiiListView;

class ListView extends YiiListView
{
    public function renderItems()
    {
        $models = $this->dataProvider->getModels();
        $keys = $this->dataProvider->getKeys();
        $rows = [];

        if (!count($models)) {
            return $this->renderEmpty();
        }

        foreach (array_values($models) as $index => $model) {
            $key = $keys[$index];
            if (($before = $this->renderBeforeItem($model, $key, $index)) !== null) {
                $rows[] = $before;
            }

            $rows[] = $this->renderItem($model, $key, $index);

            if (($after = $this->renderAfterItem($model, $key, $index)) !== null) {
                $rows[] = $after;
            }
        }


        return implode($this->separator, $rows);
    }
}