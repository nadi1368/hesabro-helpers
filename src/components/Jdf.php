<?php

namespace hesabro\helpers\components;

/* In The Name Of Allah */

use Yii;

/**
 * Class Jdf
 * @package hesabro\helpers\components
 * @author Nader <nader.bahadorii@gmail.com>
 */
class Jdf
{

    /* F */
    public static function jdate($format, $timestamp = '', $none = '', $time_zone = 'Asia/Tehran', $tr_num = 'en')
    {
        $T_sec = 0; /* <= رفع خطاي زمان سرور ، با اعداد '+' و '-' بر حسب ثانيه */

        if ($time_zone != 'local')
            date_default_timezone_set(($time_zone == '') ? 'Asia/Tehran' : $time_zone);
        $ts = $T_sec + (($timestamp == '' or $timestamp == 'now') ? time() : self::tr_num($timestamp));
        $date = explode('_', date('H_i_j_n_O_P_s_w_Y', $ts));
        [$j_y, $j_m, $j_d] = self::gregorian_to_jalali($date[8], $date[3], $date[2]);
        $doy = ($j_m < 7) ? (($j_m - 1) * 31) + $j_d - 1 : (($j_m - 7) * 30) + $j_d + 185;
        $kab = ($j_y % 33 % 4 - 1 == (int) ($j_y % 33 * .05)) ? 1 : 0;
        $sl = strlen($format);
        $out = '';
        for ($i = 0; $i < $sl; $i ++) {
            $sub = substr($format, $i, 1);
            if ($sub == '\\') {
                $out .= substr($format, ++ $i, 1);
                continue;
            }
            switch ($sub) {

                case 'E':
                case 'R':
                case 'x':
                case 'X':
                    $out .= 'http://jdf.scr.ir';
                    break;

                case 'B':
                case 'e':
                case 'g':
                case 'G':
                case 'h':
                case 'I':
                case 'T':
                case 'u':
                case 'Z':
                    $out .= date($sub, $ts);
                    break;

                case 'a':
                    $out .= ($date[0] < 12) ? 'ق.ظ' : 'ب.ظ';
                    break;

                case 'A':
                    $out .= ($date[0] < 12) ? 'قبل از ظهر' : 'بعد از ظهر';
                    break;

                case 'b':
                    $out .= (int) ($j_m / 3.1) + 1;
                    break;

                case 'c':
                    $out .= $j_y . '/' . $j_m . '/' . $j_d . ' ،' . $date[0] . ':' . $date[1] . ':' . $date[6] . ' ' . $date[5];
                    break;

                case 'C':
                    $out .= (int) (($j_y + 99) / 100);
                    break;

                case 'd':
                    $out .= ($j_d < 10) ? '0' . $j_d : $j_d;
                    break;

                case 'D':
                    $out .= self::jdate_words(array(
                        'kh' => $date[7]
                    ), ' ');
                    break;

                case 'f':
                    $out .= self::jdate_words(array(
                        'ff' => $j_m
                    ), ' ');
                    break;

                case 'F':
                    $out .= self::jdate_words(array(
                        'mm' => $j_m
                    ), ' ');
                    break;

                case 'H':
                    $out .= $date[0];
                    break;

                case 'i':
                    $out .= $date[1];
                    break;

                case 'j':
                    $out .= $j_d;
                    break;

                case 'J':
                    $out .= self::jdate_words(array(
                        'rr' => $j_d
                    ), ' ');
                    break;

                case 'k':
                    $out .= self::tr_num(100 - (int) ($doy / ($kab + 365) * 1000) / 10, $tr_num);
                    break;

                case 'K':
                    $out .= self::tr_num((int) ($doy / ($kab + 365) * 1000) / 10, $tr_num);
                    break;

                case 'l':
                    $out .= self::jdate_words(array(
                        'rh' => $date[7]
                    ), ' ');
                    break;

                case 'L':
                    $out .= $kab;
                    break;

                case 'm':
                    $out .= ($j_m > 9) ? $j_m : '0' . $j_m;
                    break;

                case 'M':
                    $out .= self::jdate_words(array(
                        'km' => $j_m
                    ), ' ');
                    break;

                case 'n':
                    $out .= $j_m;
                    break;

                case 'N':
                    $out .= $date[7] + 1;
                    break;

                case 'o':
                    $jdw = ($date[7] == 6) ? 0 : $date[7] + 1;
                    $dny = 364 + $kab - $doy;
                    $out .= ($jdw > ($doy + 3) and $doy < 3) ? $j_y - 1 : (((3 - $dny) > $jdw and $dny < 3) ? $j_y + 1 : $j_y);
                    break;

                case 'O':
                    $out .= $date[4];
                    break;

                case 'p':
                    $out .= self::jdate_words(array(
                        'mb' => $j_m
                    ), ' ');
                    break;

                case 'P':
                    $out .= $date[5];
                    break;

                case 'q':
                    $out .= self::jdate_words(array(
                        'sh' => $j_y
                    ), ' ');
                    break;

                case 'Q':
                    $out .= $kab + 364 - $doy;
                    break;

                case 'r':
                    $key = self::jdate_words(array(
                        'rh' => $date[7],
                        'mm' => $j_m
                    ));
                    $out .= $date[0] . ':' . $date[1] . ':' . $date[6] . ' ' . $date[4] . ' ' . $key['rh'] . '، ' . $j_d . ' ' . $key['mm'] . ' ' . $j_y;
                    break;

                case 's':
                    $out .= $date[6];
                    break;

                case 'S':
                    $out .= 'ام';
                    break;

                case 't':
                    $out .= ($j_m != 12) ? (31 - (int) ($j_m / 6.5)) : ($kab + 29);
                    break;

                case 'U':
                    $out .= $ts;
                    break;

                case 'v':
                    $out .= self::jdate_words(array(
                        'ss' => substr($j_y, 2, 2)
                    ), ' ');
                    break;

                case 'V':
                    $out .= self::jdate_words(array(
                        'ss' => $j_y
                    ), ' ');
                    break;

                case 'w':
                    $out .= ($date[7] == 6) ? 0 : $date[7] + 1;
                    break;

                case 'W':
                    $avs = (($date[7] == 6) ? 0 : $date[7] + 1) - ($doy % 7);
                    if ($avs < 0)
                        $avs += 7;
                    $num = (int) (($doy + $avs) / 7);
                    if ($avs < 4) {
                        $num ++;
                    } elseif ($num < 1) {
                        $num = ($avs == 4 or $avs == (($j_y % 33 % 4 - 2 == (int) ($j_y % 33 * .05)) ? 5 : 4)) ? 53 : 52;
                    }
                    $aks = $avs + $kab;
                    if ($aks == 7)
                        $aks = 0;
                    $out .= (($kab + 363 - $doy) < $aks and $aks < 3) ? '01' : (($num < 10) ? '0' . $num : $num);
                    break;

                case 'y':
                    $out .= substr($j_y, 2, 2);
                    break;

                case 'Y':
                    $out .= $j_y;
                    break;

                case 'z':
                    $out .= $doy;
                    break;

                default:
                    $out .= $sub;
            }
        }
        return ($tr_num != 'en') ? self::tr_num($out, 'fa', '.') : $out;
    }

    /* F */
    public static function jstrftime($format, $timestamp = '', $none = '', $time_zone = 'Asia/Tehran', $tr_num = 'fa')
    {
        $T_sec = 0; /* <= رفع خطاي زمان سرور ، با اعداد '+' و '-' بر حسب ثانيه */

        if ($time_zone != 'local')
            date_default_timezone_set(($time_zone == '') ? 'Asia/Tehran' : $time_zone);
        $ts = $T_sec + (($timestamp == '' or $timestamp == 'now') ? time() : self::tr_num($timestamp));
        $date = explode('_', date('h_H_i_j_n_s_w_Y', $ts));
        [$j_y, $j_m, $j_d] = self::gregorian_to_jalali($date[7], $date[4], $date[3]);
        $doy = ($j_m < 7) ? (($j_m - 1) * 31) + $j_d - 1 : (($j_m - 7) * 30) + $j_d + 185;
        $kab = ($j_y % 33 % 4 - 1 == (int) ($j_y % 33 * .05)) ? 1 : 0;
        $sl = strlen($format);
        $out = '';
        for ($i = 0; $i < $sl; $i ++) {
            $sub = substr($format, $i, 1);
            if ($sub == '%') {
                $sub = substr($format, ++ $i, 1);
            } else {
                $out .= $sub;
                continue;
            }
            switch ($sub) {

                /* Day */
                case 'a':
                    $out .= self::jdate_words(array(
                        'kh' => $date[6]
                    ), ' ');
                    break;

                case 'A':
                    $out .= self::jdate_words(array(
                        'rh' => $date[6]
                    ), ' ');
                    break;

                case 'd':
                    $out .= ($j_d < 10) ? '0' . $j_d : $j_d;
                    break;

                case 'e':
                    $out .= ($j_d < 10) ? ' ' . $j_d : $j_d;
                    break;

                case 'j':
                    $out .= str_pad($doy + 1, 3, 0, STR_PAD_LEFT);
                    break;

                case 'u':
                    $out .= $date[6] + 1;
                    break;

                case 'w':
                    $out .= ($date[6] == 6) ? 0 : $date[6] + 1;
                    break;

                /* Week */
                case 'U':
                    $avs = (($date[6] < 5) ? $date[6] + 2 : $date[6] - 5) - ($doy % 7);
                    if ($avs < 0)
                        $avs += 7;
                    $num = (int) (($doy + $avs) / 7) + 1;
                    if ($avs > 3 or $avs == 1)
                        $num --;
                    $out .= ($num < 10) ? '0' . $num : $num;
                    break;

                case 'V':
                    $avs = (($date[6] == 6) ? 0 : $date[6] + 1) - ($doy % 7);
                    if ($avs < 0)
                        $avs += 7;
                    $num = (int) (($doy + $avs) / 7);
                    if ($avs < 4) {
                        $num ++;
                    } elseif ($num < 1) {
                        $num = ($avs == 4 or $avs == (($j_y % 33 % 4 - 2 == (int) ($j_y % 33 * .05)) ? 5 : 4)) ? 53 : 52;
                    }
                    $aks = $avs + $kab;
                    if ($aks == 7)
                        $aks = 0;
                    $out .= (($kab + 363 - $doy) < $aks and $aks < 3) ? '01' : (($num < 10) ? '0' . $num : $num);
                    break;

                case 'W':
                    $avs = (($date[6] == 6) ? 0 : $date[6] + 1) - ($doy % 7);
                    if ($avs < 0)
                        $avs += 7;
                    $num = (int) (($doy + $avs) / 7) + 1;
                    if ($avs > 3)
                        $num --;
                    $out .= ($num < 10) ? '0' . $num : $num;
                    break;

                /* Month */
                case 'b':
                case 'h':
                    $out .= self::jdate_words(array(
                        'km' => $j_m
                    ), ' ');
                    break;

                case 'B':
                    $out .= self::jdate_words(array(
                        'mm' => $j_m
                    ), ' ');
                    break;

                case 'm':
                    $out .= ($j_m > 9) ? $j_m : '0' . $j_m;
                    break;

                /* Year */
                case 'C':
                    $out .= substr($j_y, 0, 2);
                    break;

                case 'g':
                    $jdw = ($date[6] == 6) ? 0 : $date[6] + 1;
                    $dny = 364 + $kab - $doy;
                    $out .= substr(($jdw > ($doy + 3) and $doy < 3) ? $j_y - 1 : (((3 - $dny) > $jdw and $dny < 3) ? $j_y + 1 : $j_y), 2, 2);
                    break;

                case 'G':
                    $jdw = ($date[6] == 6) ? 0 : $date[6] + 1;
                    $dny = 364 + $kab - $doy;
                    $out .= ($jdw > ($doy + 3) and $doy < 3) ? $j_y - 1 : (((3 - $dny) > $jdw and $dny < 3) ? $j_y + 1 : $j_y);
                    break;

                case 'y':
                    $out .= substr($j_y, 2, 2);
                    break;

                case 'Y':
                    $out .= $j_y;
                    break;

                /* Time */
                case 'H':
                    $out .= $date[1];
                    break;

                case 'I':
                    $out .= $date[0];
                    break;

                case 'l':
                    $out .= ($date[0] > 9) ? $date[0] : ' ' . (int) $date[0];
                    break;

                case 'M':
                    $out .= $date[2];
                    break;

                case 'p':
                    $out .= ($date[1] < 12) ? 'قبل از ظهر' : 'بعد از ظهر';
                    break;

                case 'P':
                    $out .= ($date[1] < 12) ? 'ق.ظ' : 'ب.ظ';
                    break;

                case 'r':
                    $out .= $date[0] . ':' . $date[2] . ':' . $date[5] . ' ' . (($date[1] < 12) ? 'قبل از ظهر' : 'بعد از ظهر');
                    break;

                case 'R':
                    $out .= $date[1] . ':' . $date[2];
                    break;

                case 'S':
                    $out .= $date[5];
                    break;

                case 'T':
                    $out .= $date[1] . ':' . $date[2] . ':' . $date[5];
                    break;

                case 'X':
                    $out .= $date[0] . ':' . $date[2] . ':' . $date[5];
                    break;

                case 'z':
                    $out .= date('O', $ts);
                    break;

                case 'Z':
                    $out .= date('T', $ts);
                    break;

                /* Time and Date Stamps */
                case 'c':
                    $key = self::jdate_words(array(
                        'rh' => $date[6],
                        'mm' => $j_m
                    ));
                    $out .= $date[1] . ':' . $date[2] . ':' . $date[5] . ' ' . date('P', $ts) . ' ' . $key['rh'] . '، ' . $j_d . ' ' . $key['mm'] . ' ' . $j_y;
                    break;

                case 'D':
                    $out .= substr($j_y, 2, 2) . '/' . (($j_m > 9) ? $j_m : '0' . $j_m) . '/' . (($j_d < 10) ? '0' . $j_d : $j_d);
                    break;

                case 'F':
                    $out .= $j_y . '-' . (($j_m > 9) ? $j_m : '0' . $j_m) . '-' . (($j_d < 10) ? '0' . $j_d : $j_d);
                    break;

                case 's':
                    $out .= $ts;
                    break;

                case 'x':
                    $out .= substr($j_y, 2, 2) . '/' . (($j_m > 9) ? $j_m : '0' . $j_m) . '/' . (($j_d < 10) ? '0' . $j_d : $j_d);
                    break;

                /* Miscellaneous */
                case 'n':
                    $out .= "\n";
                    break;

                case 't':
                    $out .= "\t";
                    break;

                case '%':
                    $out .= '%';
                    break;

                default:
                    $out .= $sub;
            }
        }
        return ($tr_num != 'en') ? self::tr_num($out, 'fa', '.') : $out;
    }

    /* F */
    public static function jmktime($h = '', $m = '', $s = '', $jm = '', $jd = '', $jy = '', $is_dst = -1)
    {
        $h = self::tr_num($h);
        $m = self::tr_num($m);
        $s = self::tr_num($s);
        $jm = self::tr_num($jm);
        $jd = self::tr_num($jd);
        $jy = self::tr_num($jy);
        if ($h == '' and $m == '' and $s == '' and $jm == '' and $jd == '' and $jy == '') {
            return mktime();
        } else {
            [$year, $month, $day] = self::jalali_to_gregorian($jy, $jm, $jd);
            return mktime($h, $m, $s, $month, $day, $year);
        }
    }

    /* F */
    public static function jgetdate($timestamp = '', $none = '', $tz = 'Asia/Tehran', $tn = 'en')
    {
        $ts = ($timestamp == '') ? time() : self::tr_num($timestamp);
        $jdate = explode('_', self::jdate('F_G_i_j_l_n_s_w_Y_z', $ts, '', $tz, $tn));
        return array(
            'seconds' => self::tr_num((int) self::tr_num($jdate[6]), $tn),
            'minutes' => self::tr_num((int) self::tr_num($jdate[2]), $tn),
            'hours' => $jdate[1],
            'mday' => $jdate[3],
            'wday' => $jdate[7],
            'mon' => $jdate[5],
            'year' => $jdate[8],
            'yday' => $jdate[9],
            'weekday' => $jdate[4],
            'month' => $jdate[0],
            0 => self::tr_num($ts, $tn)
        );
    }

    /* F */
    public static function jcheckdate($jm, $jd, $jy)
    {
        $jm = self::tr_num($jm);
        $jd = self::tr_num($jd);
        $jy = self::tr_num($jy);
        $l_d = ($jm == 12) ? (($jy % 33 % 4 - 1 == (int) ($jy % 33 * .05)) ? 30 : 29) : 31 - (int) ($jm / 6.5);
        return ($jm > 0 and $jd > 0 and $jy > 0 and $jm < 13 and $jd <= $l_d) ? true : false;
    }

    /* F */
    public static function tr_num($str, $mod = 'en', $mf = '٫')
    {
        $num_a = array(
            '0',
            '1',
            '2',
            '3',
            '4',
            '5',
            '6',
            '7',
            '8',
            '9',
            '.'
        );
        $key_a = array(
            '۰',
            '۱',
            '۲',
            '۳',
            '۴',
            '۵',
            '۶',
            '۷',
            '۸',
            '۹',
            $mf
        );
        return ($mod == 'fa') ? Yii::$app->phpNewVer->strReplace($num_a, $key_a, $str) : Yii::$app->phpNewVer->strReplace($key_a, $num_a, $str);
    }

    /* F */
    public static function jdate_words($array, $mod = '')
    {
        foreach ($array as $type => $num) {
            $num = (int) self::tr_num($num);
            switch ($type) {

                case 'ss':
                    $sl = strlen($num);
                    $xy3 = substr($num, 2 - $sl, 1);
                    $h3 = $h34 = $h4 = '';
                    if ($xy3 == 1) {
                        $p34 = '';
                        $k34 = array(
                            'ده',
                            'یازده',
                            'دوازده',
                            'سیزده',
                            'چهارده',
                            'پانزده',
                            'شانزده',
                            'هفده',
                            'هجده',
                            'نوزده'
                        );
                        $h34 = $k34[substr($num, 2 - $sl, 2) - 10];
                    } else {
                        $xy4 = substr($num, 3 - $sl, 1);
                        $p34 = ($xy3 == 0 or $xy4 == 0) ? '' : ' و ';
                        $k3 = array(
                            '',
                            '',
                            'بیست',
                            'سی',
                            'چهل',
                            'پنجاه',
                            'شصت',
                            'هفتاد',
                            'هشتاد',
                            'نود'
                        );
                        $h3 = $k3[$xy3];
                        $k4 = array(
                            '',
                            'یک',
                            'دو',
                            'سه',
                            'چهار',
                            'پنج',
                            'شش',
                            'هفت',
                            'هشت',
                            'نه'
                        );
                        $h4 = $k4[$xy4];
                    }
                    $array[$type] = (($num > 99) ? str_ireplace(array(
                                '12',
                                '13',
                                '14',
                                '19',
                                '20'
                            ), array(
                                'هزار و دویست',
                                'هزار و سیصد',
                                'هزار و چهارصد',
                                'هزار و نهصد',
                                'دوهزار'
                            ), substr($num, 0, 2)) . ((substr($num, 2, 2) == '00') ? '' : ' و ') : '') . $h3 . $p34 . $h34 . $h4;
                    break;

                case 'mm':
                    $key = array(
                        'فروردین',
                        'اردیبهشت',
                        'خرداد',
                        'تیر',
                        'مرداد',
                        'شهریور',
                        'مهر',
                        'آبان',
                        'آذر',
                        'دی',
                        'بهمن',
                        'اسفند'
                    );
                    $array[$type] = $key[$num - 1];
                    break;

                case 'rr':
                    $key = array(
                        'یک',
                        'دو',
                        'سه',
                        'چهار',
                        'پنج',
                        'شش',
                        'هفت',
                        'هشت',
                        'نه',
                        'ده',
                        'یازده',
                        'دوازده',
                        'سیزده',
                        'چهارده',
                        'پانزده',
                        'شانزده',
                        'هفده',
                        'هجده',
                        'نوزده',
                        'بیست',
                        'بیست و یک',
                        'بیست و دو',
                        'بیست و سه',
                        'بیست و چهار',
                        'بیست و پنج',
                        'بیست و شش',
                        'بیست و هفت',
                        'بیست و هشت',
                        'بیست و نه',
                        'سی',
                        'سی و یک'
                    );
                    $array[$type] = $key[$num - 1];
                    break;

                case 'rh':
                    $key = array(
                        'یکشنبه',
                        'دوشنبه',
                        'سه شنبه',
                        'چهارشنبه',
                        'پنجشنبه',
                        'جمعه',
                        'شنبه'
                    );
                    $array[$type] = $key[$num];
                    break;

                case 'sh':
                    $key = array(
                        'مار',
                        'اسب',
                        'گوسفند',
                        'میمون',
                        'مرغ',
                        'سگ',
                        'خوک',
                        'موش',
                        'گاو',
                        'پلنگ',
                        'خرگوش',
                        'نهنگ'
                    );
                    $array[$type] = $key[$num % 12];
                    break;

                case 'mb':
                    $key = array(
                        'حمل',
                        'ثور',
                        'جوزا',
                        'سرطان',
                        'اسد',
                        'سنبله',
                        'میزان',
                        'عقرب',
                        'قوس',
                        'جدی',
                        'دلو',
                        'حوت'
                    );
                    $array[$type] = $key[$num - 1];
                    break;

                case 'ff':
                    $key = array(
                        'بهار',
                        'تابستان',
                        'پاییز',
                        'زمستان'
                    );
                    $array[$type] = $key[(int) ($num / 3.1)];
                    break;

                case 'km':
                    $key = array(
                        'فر',
                        'ار',
                        'خر',
                        'تی‍',
                        'مر',
                        'شه‍',
                        'مه‍',
                        'آب‍',
                        'آذ',
                        'دی',
                        'به‍',
                        'اس‍'
                    );
                    $array[$type] = $key[$num - 1];
                    break;

                case 'kh':
                    $key = array(
                        'ی',
                        'د',
                        'س',
                        'چ',
                        'پ',
                        'ج',
                        'ش'
                    );
                    $array[$type] = $key[$num];
                    break;

                default:
                    $array[$type] = $num;
            }
        }
        return ($mod == '') ? $array : implode($mod, $array);
    }

    /**
     * Convertor from and to Gregorian and Jalali (Hijri_Shamsi,Solar) Functions
     * Copyright(C)2011, Reza Gholampanahi [ http://jdf.scr.ir/jdf ] version 2.50
     */

    /* F */
    public static function gregorian_to_jalali($g_y, $g_m, $g_d, $mod = '')
    {
        $g_y = self::tr_num($g_y);
        $g_m = self::tr_num($g_m);
        $g_d = self::tr_num($g_d); /* <= :اين سطر ، جزء تابع اصلي نيست */
        $d_4 = $g_y % 4;
        $g_a = array(
            0,
            0,
            31,
            59,
            90,
            120,
            151,
            181,
            212,
            243,
            273,
            304,
            334
        );
        $doy_g = $g_a[(int) $g_m] + $g_d;
        if ($d_4 == 0 and $g_m > 2)
            $doy_g ++;
        $d_33 = (int) ((($g_y - 16) % 132) * .0305);
        $a = ($d_33 == 3 or $d_33 < ($d_4 - 1) or $d_4 == 0) ? 286 : 287;
        $b = (($d_33 == 1 or $d_33 == 2) and ($d_33 == $d_4 or $d_4 == 1)) ? 78 : (($d_33 == 3 and $d_4 == 0) ? 80 : 79);
        if ((int) (($g_y - 10) / 63) == 30) {
            $a --;
            $b ++;
        }
        if ($doy_g > $b) {
            $jy = $g_y - 621;
            $doy_j = $doy_g - $b;
        } else {
            $jy = $g_y - 622;
            $doy_j = $doy_g + $a;
        }
        if ($doy_j < 187) {
            $jm = (int) (($doy_j - 1) / 31);
            $jd = $doy_j - (31 * $jm ++);
        } else {
            $jm = (int) (($doy_j - 187) / 30);
            $jd = $doy_j - 186 - ($jm * 30);
            $jm += 7;
        }
        return ($mod == '') ? array(
            $jy,
            $jm,
            $jd
        ) : $jy . $mod . $jm . $mod . $jd;
    }

    /* F */
    public static function jalali_to_gregorian($j_y, $j_m, $j_d, $mod = '')
    {
        $j_y = self::tr_num($j_y);
        $j_m = self::tr_num($j_m);
        $j_d = self::tr_num($j_d); /* <= :اين سطر ، جزء تابع اصلي نيست */
        $d_4 = ($j_y + 1) % 4;
        $doy_j = ($j_m < 7) ? (($j_m - 1) * 31) + $j_d : (($j_m - 7) * 30) + $j_d + 186;
        $d_33 = (int) ((($j_y - 55) % 132) * .0305);
        $a = ($d_33 != 3 and $d_4 <= $d_33) ? 287 : 286;
        $b = (($d_33 == 1 or $d_33 == 2) and ($d_33 == $d_4 or $d_4 == 1)) ? 78 : (($d_33 == 3 and $d_4 == 0) ? 80 : 79);
        if ((int) (($j_y - 19) / 63) == 20) {
            $a --;
            $b ++;
        }
        if ($doy_j <= $a) {
            $gy = $j_y + 621;
            $gd = $doy_j + $b;
        } else {
            $gy = $j_y + 622;
            $gd = $doy_j - $a;
        }
        foreach (array(
                     0,
                     31,
                     ($gy % 4 == 0) ? 29 : 28,
                     31,
                     30,
                     31,
                     30,
                     31,
                     31,
                     30,
                     31,
                     30,
                     31
                 ) as $gm => $v) {
            if ($gd <= $v)
                break;
            $gd -= $v;
        }
        return ($mod == '') ? array(
            $gy,
            $gm,
            $gd
        ) : $gy . $mod . $gm . $mod . $gd;
    }

    public static  function Convert_jalali_to_gregorian($date)
    {
        if(!empty($date) && self::pregMatchDate($date))
        {
            [$j_y, $j_m, $j_d]=  explode('/', $date);
            [$year, $month, $day]=self::jalali_to_gregorian($j_y, $j_m, $j_d);

            return $year.'/'.$month.'/'.$day;
        }
        else {
            return date('Y/m/d');
        }
    }

    /*
       * بررسی صحت تاریخ
       */
    public static  function ValidateDate($date){
        $match=self::pregMatchDate($date);
        if($match)
        {
            $str_time= strtotime(self::Convert_jalali_to_gregorian($date));
            $change_date=self::jdate("Y/m/d",$str_time);
            return $date==$change_date ? true : false;
        }else
        {
            return false;
        }
    }

    public static  function pregMatchDate($date){
        return preg_match('#^([0-9]?[0-9]?[0-9]{2}[- /.](0?[1-9]|1[012])[- /.](0?[1-9]|[12][0-9]|3[01]))*$#', $date);
    }

    /**
     * @param $y
     * @param $m
     * @param $d
     * @param $distance
     * قسط بعدی
     */
    public static function nextPayment($y,$m,$d,$distance)
    {
        $m+=$distance;
        if($m>12)
        {
            // سال بعد
            $i=(int)($m/12);
            if($m%12==0)
            {
                $m=12;
                $y+=$i-1;
            }else
            {
                $m=$m%12;
                $y+=$i;
            }
        }

        if($d<10 && strlen($d)==1)
        {
            $d='0'.$d;
        }

        if($m<10 && strlen($m)==1)
        {
            $m='0'.$m;
        }
        $date="$y/$m/$d";

        if(!self::ValidateDate($date))
        {
            $d--;
            $date="$y/$m/$d";

            if($d<10)
            {
                $d='0'.$d;
            }

            if(!self::ValidateDate($date))
            {
                $d--;
                $date="$y/$m/$d";
            }
        }

        return $date;
    }

    /**
     * @param $jdate
     * @param $distance
     * @return string
     */
    public static function nextPaymentJalayDate($jdate,$distance)
    {
        $jdate = date_parse_from_format("Y/m/d", $jdate);
        return self::nextPayment($jdate['year'],$jdate['month'],$jdate['day'],$distance);
    }

    public static function nextMonth($ym)
    {
        $exp = explode("/", $ym);
        $y = $exp[0];
        $m = $exp[1];
        if ($m == 12) {
            $m = 1;
            $y++;
        } else {
            $m++;
        }

        if($m<10)
        {
            $m='0'.$m;
        }
        return $y . "/" . $m;
    }

    public static function preMonth($ym)
    {
        $exp = explode("/", $ym);
        $y = $exp[0];
        $m = $exp[1];
        if ($m == 1) {
            $m = 12;
            $y--;
        } else {
            $m--;
        }
        if($m<10)
        {
            $m='0'.$m;
        }
        return $y . "/" . $m;
    }

    public static function jalaliToTimestamp($jdate, $format = "Y/m/d H:i:s", $defaultHour = 00, $defaultMinute = 00, $defaultSecond = 00)
    {
        $jdate = date_parse_from_format($format, $jdate);
        $tdate = ($jdate['error_count'] === 0) ?
            self::jmktime(
                $jdate['hour'] ? $jdate['hour'] : $defaultHour,
                $jdate['minute'] ? $jdate['minute'] : $defaultMinute,
                $jdate['second'] ? $jdate['second'] : $defaultSecond,
                $jdate['month'],
                $jdate['day'],
                $jdate['year']
            )
            :
            null;

        return $tdate;
    }

    public static function lastDayInMonth($year, $month)
    {
        $month=$month<10 ? '0'.$month : $month;
        $date = $year . '/' . $month . '/31';
        if(!self::ValidateDate($date))
        {
            $date = $year  . '/'. $month . '/30';
            if(!self::ValidateDate($date))
            {
                return '29';
            }else
            {
                return '30';
            }
        }else
        {
            return '31';
        }
    }

    public static function getStartAndEndOfCurrentMonth($currentTime='')
    {
        $start_of_the_month = self::jdate('Y-m-01', $currentTime, '', 'Asia/Tehran', 'en');
        $end_of_the_month = self::jdate('Y-m-t', $currentTime, '', 'Asia/Tehran', 'en');

        $start_of_the_month_parse = date_parse($start_of_the_month);
        $end_of_the_month_parse = date_parse($end_of_the_month);

        $start_of_the_month_timestamp = self::jmktime(00, 00, 00, $start_of_the_month_parse['month'], $start_of_the_month_parse['day'], $start_of_the_month_parse['year']);
        $end_of_the_month_timestamp = self::jmktime(23, 59, 59, $end_of_the_month_parse['month'], $end_of_the_month_parse['day'], $end_of_the_month_parse['year']);

        return [
            0 => $start_of_the_month_timestamp,
            1 => $end_of_the_month_timestamp,
        ];
    }

    public static function getStartAndEndOfPreMonth()
    {

        $preMonth=Jdf::preMonth(self::jdate('Y/m')). self::jdate('/d');
//        list($y,$m,$d)=explode('/',$preMonth);
        $timestamp=strtotime(self::Convert_jalali_to_gregorian($preMonth));
        $start_of_the_month = self::jdate('Y-m-01', $timestamp);
        $end_of_the_month = self::jdate('Y-m-t', $timestamp);

        $start_of_the_month_parse = date_parse($start_of_the_month);
        $end_of_the_month_parse = date_parse($end_of_the_month);

        $start_of_the_month_timestamp = self::jmktime(00, 00, 00, $start_of_the_month_parse['month'], $start_of_the_month_parse['day'], $start_of_the_month_parse['year']);
        $end_of_the_month_timestamp = self::jmktime(23, 59, 59, $end_of_the_month_parse['month'], $end_of_the_month_parse['day'], $end_of_the_month_parse['year']);

        return [
            0 => $start_of_the_month_timestamp,
            1 => $end_of_the_month_timestamp,
        ];
    }

    public static function getStartAndEndOfCurrentYear($currentTime='')
    {
        $start_of_the_year = self::jdate('Y-01-01', $currentTime);
        $end_of_the_year = self::jdate('Y-12-t', $currentTime);

        $start_of_the_year_parse = date_parse($start_of_the_year);
        $end_of_the_year_parse = date_parse($end_of_the_year);

        $start_of_the_year_timestamp = self::jmktime(00, 00, 00, $start_of_the_year_parse['month'], $start_of_the_year_parse['day'], $start_of_the_year_parse['year']);
        $end_of_the_year_timestamp = self::jmktime(23, 59, 59, $end_of_the_year_parse['month'], $end_of_the_year_parse['day'], $end_of_the_year_parse['year']);

        return [
            'start' => $start_of_the_year_timestamp,
            'end'   => $end_of_the_year_timestamp,
        ];
    }



    public static function getStartAndEndOfCurrentMonthMbtVersion($type = 'time',$currentTime='')
    {
        $start_of_the_month = self::jdate('Y/m/01', $currentTime);
        $end_of_the_month = self::jdate('Y/m/t', $currentTime);

        if ($type == 'time') {
            return [
                strtotime(self::Convert_jalali_to_gregorian($start_of_the_month)),
                strtotime(self::Convert_jalali_to_gregorian($end_of_the_month)),
            ];
        } else {
            return [
                $start_of_the_month,
                $end_of_the_month,
            ];
        }
    }

    public static function getStartAndEndOfCurrentYearMbtVersion($type = 'time',$currentTime='')
    {
        $start_of_the_year = self::jdate('Y/01/01', $currentTime);
        $end_of_the_year = self::jdate('Y/12/t', $currentTime);

        if ($type == 'time') {
            return [
                'start' => strtotime(self::Convert_jalali_to_gregorian($start_of_the_year)),
                'end' => strtotime(self::Convert_jalali_to_gregorian($end_of_the_year)),
            ];
        } else {
            return [
                'start' => $start_of_the_year,
                'end' => $end_of_the_year,
            ];
        }
    }

    public static function getStartAndEndOfPreMonthMbtVersion($type = 'time')
    {
        $preMonth = Jdf::preMonth(self::jdate('Y/m')) . self::jdate('/d');
        $timestamp = strtotime(self::Convert_jalali_to_gregorian($preMonth));
        $start_of_the_month = self::jdate('Y/m/01', $timestamp);
        $end_of_the_month = self::jdate('Y/m/t', $timestamp);

        if ($type == 'time') {
            return [
                strtotime(self::Convert_jalali_to_gregorian($start_of_the_month)),
                strtotime(self::Convert_jalali_to_gregorian($end_of_the_month)),
            ];
        } else {
            return [
                $start_of_the_month,
                $end_of_the_month,
            ];
        }
    }

    public static function diffDay($startDate,$endDate, $fromNextDay = false)
    {
        $start_time = strtotime(self::Convert_jalali_to_gregorian($startDate) . ($fromNextDay ? ' 23:59:59' : ' 00:00:00'));
        $end_time = strtotime(self::Convert_jalali_to_gregorian($endDate) . ' 23:59:59');
        return round(abs($end_time - $start_time) / (60 * 60 * 24));
    }

    public static function plusDay($date, $day, string $type = "plus") : string
    {
        $gDate = self::Convert_jalali_to_gregorian($date);
        if ($type == "plus") {
            $plusDay = strtotime('+' . $day . ' day', strtotime($gDate));
        } else {

            $plusDay = strtotime('-' . $day . ' day', strtotime($gDate));
        }

        return self::jdate("Y/m/d", $plusDay);
    }

    public static function plusYear($date, $year)
    {
        $gDate = self::Convert_jalali_to_gregorian($date);

        $plusDay = strtotime('+' . $year . ' year', strtotime($gDate));

        return self::jdate("Y/m/d", $plusDay);

    }

    public static function hasLeapYear($date)
    {
        $exp = explode('/', $date);
        $year = $exp[0];

        $ary = [1, 5, 9, 13, 17, 22, 26, 30];
        $b = $year % 33;
        if (in_array($b, $ary))
            return true;
        return false;
    }

    /**
     * Returns correct names for week days
     */
    private static function getDayNames($day, $shorten = false, $len = 1, $numeric = false)
    {
        $days = array(
            'sat' => array(1, 'شنبه'),
            'sun' => array(2, 'یکشنبه'),
            'mon' => array(3, 'دوشنبه'),
            'tue' => array(4, 'سه شنبه'),
            'wed' => array(5, 'چهارشنبه'),
            'thu' => array(6, 'پنجشنبه'),
            'fri' => array(7, 'جمعه')
        );

        $day = substr(strtolower($day), 0, 3);
        $day = $days[$day];

        return ($numeric) ? $day[0] : (($shorten) ? self::substr($day[1], 0, $len) : $day[1]);
    }

    /**
     * Returns correct names for months
     */
    public static function getMonthNames($month, $shorten = false, $len = 3)
    {
        // Convert
        $months = array(
            'فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور', 'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند'
        );
        $ret    = $months[$month - 1];

        // Return
        return ($shorten) ? self::substr($ret, 0, $len) : $ret;
    }

    /**
     * Substring helper
     */
    private static function substr($str, $start, $len)
    {
        if( function_exists('mb_substr') ){
            return mb_substr($str, $start, $len, 'UTF-8');
        }
        else{
            return substr($str, $start, $len * 2);
        }
    }

    function add_month(string $date, int $month = 1): string
    {
        $timestamp = is_numeric($date) ? $date : Jdf::jalaliToTimestamp($date);
        [$y, $m] = explode('/', $this->jdate('Y/m/d', $timestamp, tr_num: 'en'));
        $lastDay = $this->jdate('t', $timestamp, tr_num: 'en');
        $nextMonth = $this->jdate('Y/m/d', $this->jmktime('23', '59', '59', $m, $lastDay, $y) + 1, tr_num: 'en');
        return $month > 1 ? $this->add_month($nextMonth, $month - 1) : $nextMonth;
    }
}

/* [ jdf.php ] version 2.55 ?> Download new version from [ http://jdf.scr.ir ] */
