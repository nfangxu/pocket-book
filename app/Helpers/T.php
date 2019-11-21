<?php


namespace App\Helpers;


class T
{
    /**
     * 获取开始日期和结束日期中间的所有日期
     *
     * @param $start
     * @param $end
     * @param int $skip
     * @return array
     */
    public static function dates($start, $end, $skip = 0)
    {
        $start = strtotime($start);
        $end = strtotime($end);

        $dates = [];

        // 开始跳过 $skip 天
        $start += $skip * 24 * 60 * 60;

        do {
            array_push($dates, date("Y-m-d", $start));
            $start += 24 * 60 * 60;
        } while ($start <= $end);

        return $dates;
    }
}
