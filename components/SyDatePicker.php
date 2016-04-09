<?php
namespace yariksav\actives\components;

use app\modules\sy\Module;

class SyDatePicker
{
    public static $format;

    static public function space()
    {
        $space = '\'';
        if ( (PHP_MAJOR_VERSION > 5) || ((PHP_MAJOR_VERSION == 5) && (PHP_MINOR_VERSION > 3)) ) {
            $space = '&nbsp;';
        }
        return $space;
    }

    static public function dateRanges($format)
    {
        $firstDayFormat = str_replace('d', '01', $format);
        $firstDayFirstMonthFormat = str_replace('m', '01', $firstDayFormat);
        $lastDayFormat = str_replace('m', '12', str_replace('d', '31', $firstDayFormat));

        return [
            Module::t('app', 'Today')=>array(date($format, strtotime('today')), date($format, strtotime('today'))),
            Module::t('app', 'Current week')=>array(date($format, date('w')?strtotime('this week'):strtotime('last week')), date($format, strtotime('today'))),
            Module::t('app', 'Last week')=>array(date($format, strtotime('previous week monday')), date($format, strtotime('previous week sunday'))),
            Module::t('app', 'Current month')=>array(date($firstDayFormat, strtotime('this month')), date($format, strtotime('today'))),
            Module::t('app', 'Last month')=>array(date($firstDayFormat, strtotime('previous month')), date($format, strtotime('previous month'))),
            Module::t('app', 'Current year')=>array(date($firstDayFirstMonthFormat, strtotime('this year')), date($format, strtotime('today'))),
            Module::t('app', 'Last year')=>array(date($firstDayFirstMonthFormat, strtotime('previous year')), date($lastDayFormat, strtotime('previous year'))),
        ];
    }

    // TODO: Возможно было бы красивее просто переписать виджет Датапикера
    static public function datePickerLocale()
    {
        return array(
                'applyLabel'=>Module::t('app', 'Apply'),
                'fromLabel'=>Module::t('app', 'From'),
                'toLabel'=>Module::t('app', 'To'),
                'daysOfWeek'=>DateLabels::dowShort2Sunday(),
                'monthNames'=>DateLabels::monthWith0(),
                'customRangeLabel'=>Module::t('app', 'Custom Range'),
                'firstDay'=>1,
        );
    }

    static public function getDateRangeFromPostRequest($value)
    {
        if (!is_string($value))
            return false;
        $rangeRaw = explode('-', $value);

        if ( !isset($rangeRaw[0]) or empty($rangeRaw[0]) )
            return false;
        if ( !isset($rangeRaw[1]) or empty($rangeRaw[1]) )
            return false;

        $range['start'] = strtotime(trim($rangeRaw[0]));
        $range['end'] = strtotime(trim($rangeRaw[1]));

        if (!$range['start'] or !$range['end'])
            return false;
        if ($range['end'] < $range['start'])
            return false;

        return $range;
    }

    static public function getDefautRange($range)
    {
        $range = [
            'start' => strtotime('today') - $range,
            'end' => strtotime('today')
        ];
        return $range;
    }

    static public function formatDateRangeForDb(&$start, &$end, $format='Y-m-d')
    {
        if ( !is_int($start) or !is_int($end) )
            return false;
        if ($start > $end)
            return false;

        $start = date($format.' 00:00:00', $start);
        $end   = date($format.' 24:00:00', $end);

        if (!$start or !$end)
            return false;
        return true;
    }

    static public function formatDateRangeForDatePicker(&$range)
    {
        if ( !is_int($range['start']) or !is_int($range['end']) )
            return false;
        if ($range['start'] > $range['end'])
            return false;

        $range['start'] = date('d.m.Y', $range['start']);
        $range['end']   = date('d.m.Y', $range['end']);

        if (!$range['start'] or !$range['end'])
            return false;

        return true;
    }
}
