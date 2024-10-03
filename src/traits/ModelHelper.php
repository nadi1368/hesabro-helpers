<?php

namespace hesabro\helpers\traits;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

trait ModelHelper
{
    /**
     * Creates and populates a set of models.
     *
     * @param string $modelClass
     * @param array $multipleModels
     * @return array
     */
    public static function createMultiple($modelClass, $multipleModels = [], $data = false, $primaryField = 'id')
    {
        $model    = new $modelClass;
        $formName = $model->formName();
        $post     = $data ?: Yii::$app->request->post($formName);
        $models   = [];

        if (!empty($multipleModels)) {
            $keys = array_keys(ArrayHelper::map($multipleModels, $primaryField, $primaryField));
            $multipleModels = array_combine($keys, $multipleModels);
        }

        if ($post && is_array($post)) {
            foreach ($post as $i => $item) {
                if (isset($item[$primaryField]) && !empty($item[$primaryField]) && isset($multipleModels[$item[$primaryField]])) {
                    $models[] = $multipleModels[$item[$primaryField]];
                } else {
                    $models[] = new $modelClass;
                }
            }
        }

        unset($model, $formName, $post);
        return $models;
    }

    public static function loadMultiple($models, $data, $formName = null,$scenario="default",$att=[])
    {

        if ($formName === null) {
            /* @var $first Model|false */
            $first = reset($models);
            if ($first === false) {
                return false;
            }
            $formName = $first->formName();
        }

        $success = false;
        foreach ($models as $i => $model) {
            /* @var $model Model */
            $model->scenario = $scenario;

            if ($formName == '') {
                if (!empty($data[$i]) && $model->load($data[$i], '')) {
                    $success = true;
                }
            } elseif (!empty($data[$formName][$i]) && $model->load($data[$formName][$i], '')) {
                $success = true;
            }

            if(!empty($att)){
                foreach ($att as $k =>$v) {
                    $model->$k=$v;
                }
            }
        }

        return $success;
    }

    /**
     * Creates and populates a set of models.
     *
     * @param $modelClass
     * @param $insertScenario
     * @param $updateScenario
     * @param $multipleModels
     * @param $data
     * @return array
     */
    public static function createMultipleWithScenario($modelClass, $insertScenario = 'default', $updateScenario = 'default', $multipleModels = [], $data = null)
    {
        $model = new $modelClass(['scenario' => $insertScenario]);
        $formName = $model->formName();
        // added $data=null to function arguments
        // modified the following line to accept new argument
        $post = empty($data) ? Yii::$app->request->post($formName) : $data[$formName];
        $models = [];

        if (!empty($multipleModels)) {
            $keys = array_keys(ArrayHelper::map($multipleModels, 'id', 'id'));
            $multipleModels = array_combine($keys, $multipleModels);
        }

        if ($post && is_array($post)) {
            foreach ($post as $i => $item) {
                if (isset($item['id']) && !empty($item['id']) && isset($multipleModels[$item['id']])) {
                    $multipleModels[$item['id']]['scenario'] = $updateScenario;
                    $models[] = $multipleModels[$item['id']];
                } else {
                    $models[] = new $modelClass(['scenario' => $insertScenario]);
                }
            }
        }

        unset($model, $formName, $post);

        return $models;
    }
}