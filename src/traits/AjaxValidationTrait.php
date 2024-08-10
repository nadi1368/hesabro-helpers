<?php

namespace hesabro\helpers\traits;

use Yii;
use yii\base\Model;
use yii\helpers\Html;
use yii\web\Response;
use yii\widgets\ActiveForm;

trait AjaxValidationTrait
{
    /**
     * Performs ajax validation.
     *
     * @param Model $model
     *
     * @throws \yii\base\ExitException
     */
    protected function performAjaxValidation(Model $model, $attributes = null)
    {
        if (\Yii::$app->request->isAjax && $model->load(\Yii::$app->request->post())) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            \Yii::$app->response->data = $this->validateSingaleModel($model, $attributes);
            if (!empty(Yii::$app->params['errorHiddenAjax'])) {
                Yii::$app->response->data['errorHiddenAjax'] = Yii::$app->params['errorHiddenAjax'];
            }
            \Yii::$app->response->send();
            \Yii::$app->end();
        }
    }

    /**
     * Performs ajax multiple validation.
     *
     * @param Model[] $models
     *
     * @throws \yii\base\ExitException
     */
    protected function performAjaxMultipleValidation($models, $attributes = null)
    {
        if (\Yii::$app->request->isAjax && Model::loadMultiple($models, \Yii::$app->request->post())) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            \Yii::$app->response->data = $this->validateMultipleModel($models, $attributes);
            if (!empty(Yii::$app->params['errorHiddenAjax'])) {
                Yii::$app->response->data['errorHiddenAjax'] = Yii::$app->params['errorHiddenAjax'];
            }
            \Yii::$app->response->send();
            \Yii::$app->end();
        }
    }

    /**
     * Performs ajax batch validation.
     *
     * @param array $models
     *
     * @throws \yii\base\ExitException
     */
    protected function performAjaxBatchValidation($models)
    {
        if (\Yii::$app->request->isAjax) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            $errors = [];

            foreach ($models as $index => $model) {
                if (is_array($model)) {
                    $modelErrors = ActiveForm::validateMultiple($model);
                    $errors = array_merge($errors, $modelErrors);
                } elseif ($model instanceof Model) {
                    $modelErrors = ActiveForm::validate($model);
                    $errors = array_merge($errors, $modelErrors);
                }
            }

            \Yii::$app->response->data = $errors;
            if (!empty(Yii::$app->params['errorHiddenAjax'])) {
                Yii::$app->response->data['errorHiddenAjax'] = Yii::$app->params['errorHiddenAjax'];
            }
            \Yii::$app->response->send();
            \Yii::$app->end();
        }
    }

    protected function customAjaxValidationSetError($model, $errors)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        foreach ($errors as $attribute => $error) {
            \Yii::$app->response->data = [Html::getInputId($model, $attribute) => [$error]];
            \Yii::$app->response->send();
        }

        \Yii::$app->end();
    }


    protected function performAjaxMultipleError($models, $attributes = null)
    {
        if (Yii::$app->request->isAjax && Model::loadMultiple($models, Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $this->errorMultiple($models, $attributes);
            if (!empty(Yii::$app->params['errorHiddenAjax'])) {
                Yii::$app->response->data['errorHiddenAjax'] = Yii::$app->params['errorHiddenAjax'];
            }
            Yii::$app->response->send();
            Yii::$app->end();
        }
    }

    protected static function errorMultiple($models, $attributes = null)
    {
        $result = [];
        /* @var $model Model */
        foreach ($models as $i => $model) {
            foreach ($model->getErrors() as $attribute => $errors) {
                $result[Html::getInputId($model, "[$i]" . $attribute)] = $errors;
            }
        }

        return $result;
    }

    protected static function errorSingle($model, $attributes = null)
    {
        $result = [];
        /* @var $model Model */
        foreach ($model->getErrors() as $attribute => $errors) {
            $result[Html::getInputId($model, $attribute)] = $errors;
        }
        return $result;
    }

    protected function validateSingaleModel($model, $attributes = null)
    {

        $result = [];
        if ($attributes instanceof Model) {
            // validating multiple models
            $models = func_get_args();
            $attributes = null;
        } else {
            $models = [$model];
        }
        /* @var $model Model */
        foreach ($models as $model) {
            if (!$model->hasErrors()) {
                $model->validate($attributes);
            }
            foreach ($model->getErrors() as $attribute => $errors) {
                $result[Html::getInputId($model, $attribute)] = $errors;
            }
        }

        return $result;
    }
    protected function validateMultipleModel($models, $attributes = null)
    {
        $result = [];
        /* @var $model Model */
        foreach ($models as $i => $model) {
            if (!$model->hasErrors()) {
                $model->validate($attributes);
            }
            foreach ($model->getErrors() as $attribute => $errors) {
                $result[Html::getInputId($model, "[$i]" . $attribute)] = $errors;
            }
        }

        return $result;
    }
}
