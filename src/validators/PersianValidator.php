<?php

namespace  hesabro\helpers\validators;

use Yii;
use yii\validators\Validator;
use common\components\jdf\Jdf;

/**
 * Class PersianValidator
 * @package common\validators
 * @author Nader <nader.bahadorii@gmail.com>
 */
class PersianValidator extends Validator
{
    const REPLACE_CHARACTERS = [
        'أ' => 'ا',
        'إ' => 'ا',
        'اِ' => 'ا',
        'ك' => 'ک',
        'ؤ' => 'و',
        'ة' => 'ه',
        'ۀ' => 'ه',
        'ي' => 'ی',
        'ى' => 'ی',
        '‌' => ' ',
    ];
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if ($this->message === null) {
            $this->message = 'فقط حروف فارسی معتبر هستند';
        }
    }

    public function validateAttribute($model, $attribute)
    {
        $valid = true;

        $model->{$attribute} = trim($model->{$attribute});
        $model->{$attribute} = str_replace(array_keys(self::REPLACE_CHARACTERS), array_values(self::REPLACE_CHARACTERS), $model->{$attribute});

        if (!empty($model->{$attribute}) && !preg_match('/^[ آابپتثجچحخدذرزژسشصضطظعغفقکگلمنوهیئء]+$/u', $model->{$attribute})) {
            $valid = false;
        }

        return $valid ? null : $this->addError($model, $attribute, $this->message);
    }


    public static function replaceChar($value)
    {
        $value = trim($value);
        $value = str_replace(array_keys(self::REPLACE_CHARACTERS), array_values(self::REPLACE_CHARACTERS), $value);

        return $value;
    }
}
