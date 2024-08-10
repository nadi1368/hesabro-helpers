<?php

namespace hesabro\helpers\components;

use Exception;
use Yii;
use yii\base\Component;
use yii\base\InvalidArgumentException;
use yii\helpers\Html;

/**
 * Class Helper
 * @package common\components
 * @author Nader <nader.bahadorii@gmail.com>
 */
class Helper extends Component
{

    const YES = 1;
    const NO = 2;

    const CHECKED = 1;
    const UN_CHECKED = 0;

    public function init()
    {
        parent::init();

    }


    public static function toEn($string)
    {
        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        //$arabic = ['٩', '٨', '٧', '٦', '٥', '٤', '٣', '٢', '١','٠'];
        $arabic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];

        $num = range(0, 9);
        $convertedPersianNums = \Yii::$app->phpNewVer->strReplace($persian, $num, $string);
        $englishNumbersOnly = \Yii::$app->phpNewVer->strReplace($arabic, $num, $convertedPersianNums);

        return $englishNumbersOnly;
    }


    public static function itemAlias($type, $code = NULL)
    {

        $_items = [
            'YesOrNo' => [
                self::YES => Yii::t('hesabro-helper', 'Yes'),
                self::NO => Yii::t('hesabro-helper', 'No'),
            ],
            'CheckboxTitle' => [
                self::CHECKED => Yii::t('hesabro-helper', 'Yes'),
                self::UN_CHECKED => Yii::t('hesabro-helper', 'No'),
            ],
            'CheckboxIcon' => [
                self::CHECKED => '<span class="far fa-check-circle fa-lg text-success"></span>',
                self::UN_CHECKED => '<span class="far fa-minus-circle fa-lg text-danger"></span>',
            ],
            'YesOrNoIcon' => [
                self::YES => '<span class="far fa-check-circle fa-lg text-success"></span>',
                self::NO => '<span class="far fa-minus-circle fa-lg text-danger"></span>',
            ],
        ];
        if (isset($code))
            return isset($_items[$type][$code]) ? $_items[$type][$code] : false;
        else
            return isset($_items[$type]) ? $_items[$type] : false;
    }

    /**
     * @param $balance
     * @return string
     */
    public static function currencyBalance($balance)
    {
        if ($balance == 0) {
            return '<span onclick="return copyToClipboard(' . $balance . ')"  class="text-info ltr copy">' . \Yii::$app->phpNewVer->numberFormat($balance) . '</span>';
        } elseif ($balance > 0) {
            return '<span onclick="return copyToClipboard(' . $balance . ')"  class="text-success ltr copy">' . \Yii::$app->phpNewVer->numberFormat($balance) . '</span>';
        } else {
            $balance *= -1;
            return '<span onclick="return copyToClipboard(' . ($balance) . ')"  class="text-danger ltr copy">(-' . \Yii::$app->phpNewVer->numberFormat($balance) . ')</span>';
        }
    }

    /**
     * supported formats: 'IBAN', 'Cart', 'Integer', 'IntegerArray', 'Float', 'String', 'Boolean', 'NullInteger', 'NullString', 'NullFloat', 'Any'
     *
     * @param $value
     * @param $format
     * @return mixed
     */
    public function format($value, $format)
    {
        $params = [$value];
        $method = 'formatter' . $format;
        if ($this->hasMethod($method)) {
            return call_user_func_array([$this, $method], $params);
        }

        throw new InvalidArgumentException("Unknown format type: $format");
    }

    public static function formatterIBAN($shaba)
    {
        return empty($shaba) ? $shaba : 'IR' . substr($shaba, 0, 2) . '-' . substr($shaba, 2, 4) . '-' . substr($shaba, 6, 4) . '-' . substr($shaba, 10, 4) . '-' . substr($shaba, 14, 4) . '-' . substr($shaba, 18, 4) . '-' . substr($shaba, 22, 2);
    }

    public static function formatterCart($cart)
    {
        return empty($cart) ? $cart : substr($cart, 0, 4) . '-' . substr($cart, 4, 4) . '-' . substr($cart, 8, 4) . '-' . substr($cart, 12, 4);
    }

    public function formatterInteger($value)
    {
        return (int)$value;
    }

    public function formatterNullInteger($value)
    {
        return isset($value) && $value !== '' ? (int)$value : null;
    }

    public function formatterFloat($value)
    {
        return (float)$value;
    }

    public function formatterNullFloat($value)
    {
        return isset($value) && $value !== '' ? (float)$value : null;
    }

    public function formatterString($value)
    {
        return (string)$value;
    }

    public function formatterNullString($value)
    {
        return isset($value) && $value !== '' ? (string)$value : null;
    }

    public function formatterBoolean($value)
    {
        return (boolean)$value;
    }

    public static function formatterIntegerArray($value)
    {
        if (is_array($value)) {
            $value = array_map('intval', $value);
        }
        return $value;
    }

    public static function formatterStringArray($value)
    {
        if (is_array($value)) {
            $value = array_map('strval', $value);
        }
        return $value;
    }

    public function formatterAny($value)
    {
        return $value;
    }

    public function formatterArray($value)
    {
        return (array)$value;
    }

    public static function convertObjectToArray($items)
    {
        $data = [];
        foreach ($items as $key => $value) {
            $data[] = [
                'key' => $key,
                'value' => $value
            ];
        }
        return $data;
    }


    public static function getUrlRoute()
    {
        $currentParams = Yii::$app->getRequest()->getQueryParams();
        $currentParams[0] = '/' . Yii::$app->controller->getRoute();
        return array_replace_recursive($currentParams, []);
    }

    public static function safeLogin()
    {
        return YII_DEBUG || (isset($_GET['auto_login']) && $_GET['auto_login'] == 'safe');
    }

    public static function renderLabelHelp($label, $help)
    {
        return Html::tag('span', $label, [
            'data-toggle' => 'popover',
            "data-container" => 'body',
            'data-trigger' => 'click hover',
            'data-placement' => 'top',
            'data-html' => 'true',
            'data-title' => Yii::t('hesabro-helper', 'Description'),
            'data-content' => $help,
            'style' => 'text-decoration: underline; cursor:help;'
        ]);
    }

    public static function generateRandomInt($length)
    {
        return mt_rand(pow(10, ($length - 1)), pow(10, $length) - 1);
    }

    public function arrayToString($values, $showIndex = true)
    {
        $data = '';
        foreach ($values as $index => $value) {
            if (is_array($value)) {
                $data .= $index . '<br />' . $this->arrayToString($value) . '<br />';
            } else {
                $data .= ($showIndex ? ($index . '=>') : '') . $value . '<br />';
            }
        }
        return $data;
    }

    public static function error(string $message, string $category = 'application'): void
    {
        $exception = new Exception();
        Yii::error($message . '<br><br>' . $exception->getTraceAsString(), $category);
    }


    /**
     * @param $amount
     * @param float $taxPercent
     * @return int[]
     */
    public static function calculateWage($amount, float $taxPercent = 10)
    {
        $a = $amount / (100 + $taxPercent);
        $tax = (int)(round($a * $taxPercent));
        $realAmount = (int)($amount - $tax);

        return [
            'tax' => $tax,
            'realAmount' => $realAmount
        ];
    }

    /**
     * @param $amount
     * @param float $taxPercent
     * @return int
     */
    public static function calculateTax($amount, float $taxPercent = 10)
    {
        return (int)($amount * $taxPercent / 100);
    }

    public static function makeSearchUrlShorter(string $action, string $form_name): array
    {
        $params = Yii::$app->request->queryParams;

        unset($params[$form_name]);

        array_unshift($params , $action);
        return $params;
    }

    public static function hexAdjustBrightness($hex, $steps) {
        $steps = max(-255, min(255, $steps));
        $hex = str_replace('#', '', $hex);

        if (strlen($hex) == 3) {
            $hex = str_repeat(substr($hex,0,1), 2).str_repeat(substr($hex,1,1), 2).str_repeat(substr($hex,2,1), 2);
        }

        $color_parts = str_split($hex, 2);
        $return = '#';

        foreach ($color_parts as $color) {
            $color   = hexdec($color); // Convert to decimal
            $color   = max(0,min(255,$color + $steps)); // Adjust color
            $return .= str_pad(dechex($color), 2, '0', STR_PAD_LEFT); // Make two char hex code
        }

        return $return;
    }
}