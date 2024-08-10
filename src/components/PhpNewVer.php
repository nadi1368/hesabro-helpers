<?php

namespace hesabro\helpers\components;

use yii\base\Component;

class PhpNewVer extends Component
{
    public function numberFormat($num, int $decimals = 0, ?string $decimal_separator = '.', ?string $thousands_separator = ','): string
    {
        return number_format((float)$num, $decimals, $decimal_separator, $thousands_separator);
    }

    public function trim($string,$characters = " \t\n\r\0\x0B"): string
    {
        return trim((string)$string, (string)$characters);
    }

    public function rtrim($string,$characters = " \t\n\r\0\x0B"): string
    {
        return rtrim((string)$string, (string)$characters);
    }

    public function ltrim($string,$characters = " \t\n\r\0\x0B"): string
    {
        return ltrim((string)$string, (string)$characters);
    }

    public function strReplace($search, $replace, $subject, &$count = 0): string
    {
        if(!is_array($search)){
            $search = (string)$search;
        }
        if(!is_array($replace)){
            $replace = (string)$replace;
        }
        if(!is_array($subject)){
            $subject = (string)$subject;
        }
        return str_replace($search, $replace, $subject, $count);
    }
}