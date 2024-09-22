<?php

namespace  hesabro\helpers\validators;

use Yii;
use yii\validators\Validator;

/**
 * Class IBANValidator
 * @package common\validators
 * @author Nader <nader.bahadorii@gmail.com>
 */
class IBANValidator extends Validator
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
        $valid = true;
        if (!$model->hasErrors()) {
            $code = preg_replace("/[A-Za-z]/", '', (string)$model->$attribute);
            $new_code = (substr($code, 2, 24) . '18' . '27' . substr($code, 0, 2));
            if ($this->my_bcmod($new_code, "97") != 1) {
                $valid = false;
            }
        }

        $valid ?: $this->addError($model, $attribute, $this->message);
    }

    public function validateValue($code)
    {
        $valid = true;
        $code = preg_replace("/[A-Za-z]/", '', $code);
        $new_code = (substr($code, 2, 24) . '18' . '27' . substr($code, 0, 2));
        if ($this->my_bcmod($new_code, "97") != 1) {
            $valid = false;
        }
        return $valid;
    }

    public function my_bcmod($x, $y)
    {
        try {
            // how many numbers to take at once? carefull not to exceed (int)
            $take = 5;
            $mod = '';

            do {
                $a = (int)$mod . substr($x, 0, $take);
                $x = substr($x, $take);
                $mod = $a % $y;
            } while (strlen($x));

            return (int)$mod;
        } catch (\Exception $e) {
            return false;
        }
    }
}
