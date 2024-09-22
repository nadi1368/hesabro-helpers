<?php

namespace  hesabro\helpers\validators;

use Yii;
use yii\validators\Validator;
use common\components\jdf\Jdf;

/**
 * Class DateValidator
 * @package common\validators
 * @author Nader <nader.bahadorii@gmail.com>
 */
class DateValidator extends Validator
{

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if ($this->message === null) {
            $this->message = Yii::t('yii', '{attribute} is invalid.');
        }
    }

    public function validateAttribute($model, $attribute)
    {
        $valid=true;
        if (!$model->hasErrors()) {
            if (!empty($model->$attribute) && !Jdf::ValidateDate($model->$attribute)) {
                $valid=false;
            }
        }

        return $valid ? null :  $this->addError($model, $attribute, $this->message);
    }
}
