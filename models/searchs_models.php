<?php

class Search extends Model
{

    public static $table;

    public static function className () //найти нужный класс который обрабатывает ту или иную функцию
    {
        return 'Search';
    }

    public static function tableName () //найти нужный класс который обрабатывает ту или иную функцию
    {
        return self::$table;
    }

    public static function get_request ($request,$start,$end)
    {
        //SELECT * FROM `sports` WHERE ((text LIKE '%Федун%') AND (time > '2017-08-27 00:00' ) AND (time < '2017-08-28 00:00' ) ) ORDER BY time DESC
        ///home/admin/web/naughtysight.ru/public_html/controller/search_controller.php:37:string '2017-08-27 00:00' (length=16) end

        ///home/admin/web/naughtysight.ru/public_html/controller/search_controller.php:38:string '2017-08-26 00:00' (length=16) start

        $request = mysqli_real_escape_string(static::get_db(),strip_tags($request));;
        $source = self::$table;
        $query = "SELECT * FROM `".$source."` WHERE ((text LIKE '%".$request."%') AND (time >'".$start."' ) AND (time < '".$end."')   ) ORDER BY time DESC";
        $result = mysqli_query(static::get_db(), $query);
        if (!$result)
        {
            die (mysqli_error(static::get_db()));
        }

        $all = array();
        while ($row = mysqli_fetch_assoc($result))
        { 
            $class_name = static::className();
            $one = new $class_name();
            /* @var $one Object */
            $one->load($row);
            if ( $one->error === success)
            {
                $all[] = $one;
            }

        }
        return $all;


    }


    public static function get_last_source_mark ($source)
    {
        $source = trim(strip_tags($source));

        $query = sprintf("SELECT * FROM `fetchers_log` WHERE ((`source` = '%s') AND (`ip` = 'SERVER BOT') ) ORDER BY `id` DESC LIMIT 1",$source);
        $result = mysqli_query(static::get_db(),$query);

        if (!$result) die(mysqli_error(static::get_db()));
        $last = mysqli_fetch_assoc($result);

        return date('d-m-Y H:i',$last['time']);
    }

}