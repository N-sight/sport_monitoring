<?php

class Fetcher_model
{
    public static function drain($source,$mon_day,$pre_mon,$quality)
    {
        $f = array();
        $url = trim(strip_tags($source));
        $name_crawler = ucfirst($url).'_crawler';
        $name_parser = ucfirst($url).'_parser';

        $allnews = $name_crawler::get_news ($mon_day,$pre_mon);
        $t_news = target_news($allnews,$quality);

        for ($i=0;$i<count($t_news);$i++)
        {
            $f [$i] = $name_parser::get_comments($t_news[$i]);
        }

        $target_f = target_com2($f);

        // вот тут надо слить данные в БД
        $link = db_connect();
        $last_time = get_last_source_mark($link,$url);

        source_mark($link,$url,$mon_day['sql']);
        render(array('line'=>$target_f,'allnews'=>$allnews,'res_name'=>$url,'yesterday'=>$mon_day['old']));

        mysqli_close($link);
        die();

    }
}