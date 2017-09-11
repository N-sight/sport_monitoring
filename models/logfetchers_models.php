<?php

class Logfetcher extends Model
{

    const LINES_PER_PAGE = 40;
    
    protected static $fields = array();
    protected static $field_types = array();

    public static function className () //найти нужный класс который обрабатывает ту или иную функцию
    {
        return 'Logfetcher';
    }

    public static function tableName ()
    {
        return 'fetchers_log';
    }

    public static function get_page ($page)
    {
        $start = ($page-1)*self::LINES_PER_PAGE;
        $end = self::LINES_PER_PAGE;
        $query = 'SELECT * FROM '.self::tableName().' ORDER BY `id` DESC LIMIT '.$start.','.$end;

        $result = mysqli_query(static::get_db(), $query);
        $all = array();
        while ($row = mysqli_fetch_assoc($result))
        {
            $class_name = static::className();
            $one = new $class_name();
            $one->load($row);
            if ( $one->error === success)
            {
                $all[] = $one;
            }
        }
        return $all;
    }

    public static function get_max_lines ()
    {
        $query = 'SELECT COUNT(*) FROM '.self::tableName();
        $res = mysqli_query(static::get_db(),$query);
        $row = mysqli_fetch_row($res);
        $total = $row[0]; // всего записей
        return (int) $total;
    }
}


