<?php
/**
 * User: sbraun
 * Date: 01.02.18
 * Time: 14:43
 */

class Mysql_lib
{
    /**
     * @link    https://stackoverflow.com/questions/9295616/how-to-get-list-of-dates-between-two-dates-in-mysql-select-query
     *
     * @param string $from  e.g. '2012-02-15'
     * @param string $until e.g. '2012-02-28'
     *
     * @return string SQL-Query
     */
    public function q_date_range($from = '2012-02-10', $until = '2012-02-15'): string {
        $start = '1970-01-01'; #could be more performant if it would be the from-value
        $sql = "select * from 
(select adddate('$start',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) selected_date from
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4) v
where selected_date between '$from' and '$until'";
        return $sql;
    }
}