<?php

namespace hesabro\helpers\traits;

use Yii;

trait CoreTrait
{
    protected function rangeToTimestampRange($range, $format = "Y/m/d H:i:s", $calendar = 1, $delimiter = " - ", $endRangeDefaultHour = 23, $endRangeDefaultMin = 59, $endRangeDefaultSec = 59, $day = false)
    {
        $date_range_start = null;
        $date_range_end = null;
        $date_ranges = explode($delimiter, (string)$range);
        $pDate = Yii::$app->jdf;

        if (count($date_ranges) == 2) {
            if ($calendar === 1) {
                $date_range_start = date_parse_from_format($format, $date_ranges[0]);

                $date_range_end = date_parse_from_format($format, $date_ranges[1]);


                if ($day) {
                    $date_range_start = ($date_range_start['error_count'] === 0) ?
                        $pDate->jmktime(0, 0, 0, $date_range_start['month'], $date_range_start['day'], $date_range_start['year'], '', 'Asia/Tehran')
                        :
                        null;
                    $date_range_end = ($date_range_end['error_count'] === 0) ?
                        $pDate->jmktime(0, 0, 0, $date_range_end['month'], $date_range_end['day'], $date_range_end['year'], '', 'Asia/Tehran')
                        :
                        null;
                } else {
                    $date_range_start = ($date_range_start['error_count'] === 0) ?
                        $pDate->jmktime(($date_range_start['hour'] !== false) ? (int)$date_range_start['hour'] : 00, ($date_range_start['minute'] !== false) ? (int)$date_range_start['minute'] : 00, ($date_range_start['second'] !== false) ? (int)$date_range_start['second'] : 00, $date_range_start['month'], $date_range_start['day'], $date_range_start['year'])
                        :
                        null;
                    $date_range_end = ($date_range_end['error_count'] === 0) ?
                        $pDate->jmktime(($date_range_end['hour'] !== false) ? (int)$date_range_end['hour'] : $endRangeDefaultHour, ($date_range_end['minute'] !== false) ? (int)$date_range_end['minute'] : $endRangeDefaultMin, ($date_range_end['second'] !== false) ? $date_range_end['second'] : $endRangeDefaultSec, $date_range_end['month'], $date_range_end['day'], $date_range_end['year'])
                        :
                        null;
                }
            } elseif ($calendar === 2) {
                $date_range_start = strtotime($date_ranges[0]);

                $date_range_end = strtotime($date_ranges[1]);
            }
        }

        return [
            'start' => $date_range_start,
            'end' => $date_range_end,
        ];
    }

    protected function jalaliToTimestamp($jdate, $format = "Y/m/d H:i:s", $defaultHour = 00, $defaultMinute = 00, $defaultSecond = 00)
    {
        $jdate = date_parse_from_format($format, $jdate);
        $pDate = Yii::$app->jdf;
        $tdate = ($jdate['error_count'] === 0) ?
            $pDate->jmktime(($jdate['hour'] !== false) ? (int)$jdate['hour'] : $defaultHour, ($jdate['minute'] !== false) ? (int)$jdate['minute'] : $defaultMinute, ($jdate['second'] !== false) ? (int)$jdate['second'] : $defaultSecond, $jdate['month'], $jdate['day'], $jdate['year'])
            :
            null;

        return $tdate;
    }

    protected function getStartAndEndOfDay($time_zone = 'Asia/Tehran', $time = null)
    {
        date_default_timezone_set($time_zone);
        $time = $time ?: time();
        $start = strtotime("today", $time);
        $end = strtotime("tomorrow", $start) - 1;

        return [
            'start' => $start,
            'end' => $end,
        ];
    }

    protected function getStartAndEndOfCurrentWeek($time_zone = 'Asia/Tehran', $first_day_of_the_week = 'Saturday')
    {
        date_default_timezone_set($time_zone);
        $start_of_the_week = strtotime("Last $first_day_of_the_week");

        if (strtolower(date('l')) === strtolower($first_day_of_the_week)) {
            $start_of_the_week = strtotime('today');
        }
        $end_of_the_week = $start_of_the_week + (60 * 60 * 24 * 7) - 1;

        return [
            'start' => $start_of_the_week,
            'end' => $end_of_the_week,
        ];
    }

    protected function getCurrentWeekDays($time_zone = 'Asia/Tehran', $first_day_of_the_week = 'Saturday')
    {
        $weekDays = [];
        $start_and_end_of_the_current_week = $this->getStartAndEndOfCurrentWeek($time_zone, $first_day_of_the_week);
        for ($start = $start_and_end_of_the_current_week['start']; $start <= $start_and_end_of_the_current_week['end']; $start = $start + (60 * 60 * 24)) {
            $weekDays[] = [
                'start' => $start,
                'end' => $start + (60 * 60 * 24) - 1
            ];
        }

        return $weekDays;
    }

    protected function getStartAndEndOfCurrentMonth()
    {
        return $this->getStartAndEndOfMonth();
    }

    /**
     * @param null|string $ym // e.g. 1399/03
     * @return array
     */
    protected function getStartAndEndOfMonth($ym = null)
    {
        $pDate = Yii::$app->jdf;
        $time = $ym ? $this->jalaliToTimestamp("$ym/01", "Y/m/d") : time();
        $start_of_the_month = Yii::$app->jdf->jdate('Y-m-01', $time, '', 'Asia/Tehran', 'en');
        $end_of_the_month = Yii::$app->jdf->jdate('Y-m-t', $time, '', 'Asia/Tehran', 'en');

        $start_of_the_month_parse = date_parse($start_of_the_month);
        $end_of_the_month_parse = date_parse($end_of_the_month);

        $start_of_the_month_timestamp = $pDate->jmktime(00, 00, 00, $start_of_the_month_parse['month'], $start_of_the_month_parse['day'], $start_of_the_month_parse['year'], '', 'Asia/Tehran');
        $end_of_the_month_timestamp = $pDate->jmktime(23, 59, 59, $end_of_the_month_parse['month'], $end_of_the_month_parse['day'], $end_of_the_month_parse['year'], '', 'Asia/Tehran');

        return [
            'start' => $start_of_the_month_timestamp,
            'end' => $end_of_the_month_timestamp,
        ];
    }

    protected function getStartAndEndOfYear($year = 'Y')
    {
        $year = $year == 'Y' ? Yii::$app->jdf->jdate($year, time(), '', 'Asia/Tehran', 'en') : $year;

        return [
            'start' => $this->getStartAndEndOfMonth("$year/01")['start'],
            'end' => $this->getStartAndEndOfMonth("$year/12")['end'],
        ];
    }

    protected function getMonthsCountBetweenTwoDays($min_day, $max_day)
    {
        $min_day_parse = date_parse($min_day);
        $max_day_parse = date_parse($max_day);

        $min_year = $min_day_parse['year'];
        $max_year = $max_day_parse['year'];

        $min_month = $min_day_parse['month'];
        $max_month = $max_day_parse['month'];

        return (($max_year - $min_year) * 12) + ($max_month - $min_month);
    }

    protected function getDaysBetweenTwoDays($min_day, $max_day)
    {
        $days = [];
        $min_day_parse = date_parse($min_day);
        $max_day_parse = date_parse($max_day);
        $min_day_timestamp = Yii::$app->jdf->jmktime(00, 00, 00, $min_day_parse['month'], $min_day_parse['day'], $min_day_parse['year']);
        $max_day_timestamp = Yii::$app->jdf->jmktime(00, 00, 00, $max_day_parse['month'], $max_day_parse['day'], $max_day_parse['year']);

        for ($day_timestamp = $min_day_timestamp; $day_timestamp <= $max_day_timestamp; $day_timestamp = $day_timestamp + (60 * 60 * 24)) {
            $days[Yii::$app->jdf->jdate('Y/m/d', $day_timestamp, '', 'Asia/Tehran', 'en')] = $day_timestamp;
        }

        return $days;
    }

    protected function getCurrentMonthDays()
    {
        $monthDays = [];
        $start_and_end_of_the_current_month = $this->getStartAndEndOfCurrentMonth();
        for ($start = $start_and_end_of_the_current_month['start']; $start <= $start_and_end_of_the_current_month['end']; $start = $start + (60 * 60 * 24)) {
            $monthDays[] = [
                'start' => $start,
                'end' => $start + (60 * 60 * 24) - 1
            ];
        }

        return $monthDays;
    }

    /**
     * @param null|string $ym // e.g. 1399/03
     * @return array
     */
    protected function getMonthDays($ym = null)
    {
        $monthDays = [];
        $start_and_end_of_the_current_month = $this->getStartAndEndOfMonth($ym);
        for ($start = $start_and_end_of_the_current_month['start']; $start <= $start_and_end_of_the_current_month['end']; $start = $start + (60 * 60 * 24)) {
            $monthDays[] = [
                'start' => $start,
                'end' => $start + (60 * 60 * 24) - 1
            ];
        }

        return $monthDays;
    }

    protected function isValidTimeStamp($timestamp)
    {
        return ((string)(int)$timestamp === $timestamp)
            && ($timestamp <= PHP_INT_MAX)
            && ($timestamp >= ~PHP_INT_MAX);
    }

    protected function isAssociative(array $arr)
    {
        if (array() === $arr)
            return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    protected function isMultidimensional(array $arr)
    {
        return count($arr) != count($arr, COUNT_RECURSIVE);
    }

    protected function firstMonthNumberOfSeason($monthNumber)
    {
        if ($monthNumber <= 3) {
            return 1;
        }
        if ($monthNumber >= 10) {
            return 10;
        }
        foreach (range(1, 12, 3) as $key => $value) {
            if ($monthNumber < $value) {
                return $value - 3;
            }
        }
        return null;
    }
}
