<?php

namespace hesabro\helpers\models;

use Yii;
use yii\base\Model;

/**
 * Class WageForm
 * @package backend\modules\accounting\models
 */
class WageForm extends Model
{

    const SCENARIO_HEADER = 'header';
    const SCENARIO_DETAILS = 'details';


    const WAGE_TYPE_NO = 0;//بدون کارمزد
    const WAGE_TYPE_SEPARATE = 1;//سند جداگانه بخورد
    const WAGE_TYPE_COMBINE = 2;//سند مرکب بخورد

    public $wage_amount;
    public $wage_type = 0;


    public function rules()
    {
        return [
            [['wage_amount', 'wage_type'], 'integer', 'on' => [self::SCENARIO_HEADER, self::SCENARIO_DETAILS]],
            [['wage_type'], 'in', 'range' => array_keys(self::itemAlias('TypeWage')), 'on' => [self::SCENARIO_HEADER]],
            [['wage_amount'], 'each', 'rule' => ['integer'], 'on' => [self::SCENARIO_DETAILS]],

            [['wage_amount'], 'required',
                'when' => function ($model) {
                    return $model->wage_type = self::WAGE_TYPE_NO;
                }, 'whenClient' => "function (attribute, value) {
                    return $('input[name=\"wage_type\"]:checked').val() == '1' || $('input[name=\"wage_type\"]:checked').val() == '2';
                }",
                'on' => [self::SCENARIO_DETAILS]],
        ];
    }


    public function attributeLabels()
    {
        return [
            'wage_amount' => Yii::t('app', 'Wage Amount'),
            'wage_type' => Yii::t('app', 'Wage Type'),
        ];
    }

    public static function itemAlias($type, $code = NULL)
    {

        $_items = [
            'TypeWage' => [
                self::WAGE_TYPE_NO => 'بدون کارمزد',
                self::WAGE_TYPE_COMBINE => 'کارمزد ترکیبی',
                self::WAGE_TYPE_SEPARATE => 'کارمزد جداگانه',
            ]
        ];
        if (isset($code))
            return isset($_items[$type][$code]) ? $_items[$type][$code] : false;
        else
            return isset($_items[$type]) ? $_items[$type] : false;
    }

    public function formName()
    {
        if ($this->getScenario() == self::SCENARIO_HEADER) {
            return '';
        }
        return parent::formName();
    }


}