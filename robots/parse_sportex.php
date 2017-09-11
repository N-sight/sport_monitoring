<?php

function page_sportex ($line)
{
    /*$line ['url']    = 'http://www.sport-express.ru/football/rfpl/news/cska-vyrval-pobedu-u-spartaka-1294873/';
    $line ['date']   = '12-08-2017 19:25';
    $line ['comm']   = 242;
    $line ['header'] = 'ЦСКА вырвал победу у "Спартака';*/

    $time = $line['date'];

    $blockcomm = array(); // сюда блоки с HTML комментариями все в кучу
    $comm = array(); // выделенные комментарии
    $t_comm = array(); // таргетированные комментарии, по объекту мониторинга, РЕЗУЛЬТАТ!
    $num = 1; // счетчик для страниц
    $max_page = ceil($line['comm']/30)+1;
    //deb($max_page);
    $n = 0; // счетчик для комментариев
    $k = 0; // счетчик для комментариев
    $flag_page = 0;
    $url = ''; // урл страницы где содержатся комментарии

    // блок выбора и отображения комментов по теме.

    do {
        //shortpause();

        if ($num == 1 )  { $url = $line['url']; }
        else { $url = $line['url'].'page'.$num.'/#comment_form';}

        $data = file_get_contents($url);
        $rows = explode("\n", $data); // разбираем чехарду на строчки

        // заберем блок с комментариями со страницы
        for ($i=0; $i<count($rows);$i++)
        {
            if (strpos($rows [$i], '<div class="item triangle_border">') !== false) // начало сборки
            {
                $flag_page = 1;
            }
            if (strpos($rows [$i], '<div class="se3_paginator">') !== false) // конец сборки
            {
                $flag_page = 0; // отсюда складываем в рядки
                break;
            }
            if ( $flag_page == 1)
            {
                $blockcomm[$k] = $rows[$i];
                $k++;
            }
        }

        $num++;
    } while ( $num < $max_page );

    // тут разбор HTML, блока с комментариями на отдельные еденицы массива.
    $k=0;
   //var_dump($blockcomm);die();
    for( $i=0; $i<count($blockcomm); $i++)
    {

        if (strpos($blockcomm [$i], '<p class="fs_16 darkcolor">') !== false) // каждый коммент стартует тут
        {
            $comm [$k]['username'] = iconv('windows-1251', 'utf-8', strip_tags($blockcomm [$i]));
            $comm [$k]['username'] = mb_substr( $comm [$k]['username'],12);
        }

        if (strpos($blockcomm [$i], 'class="mt_20') !== false)
        {
            $comm [$k]['text'] = '';
            $j = 0; //счетчик просмотра следующей строки
            $flag = 0; //значок отскока
            do {
                $str = iconv('windows-1251', 'utf-8', strip_tags($blockcomm [$i + $j]));
                $comm [$k]['text'] = $comm [$k]['text'].' '.$str;
                $j++;
                if (strpos($blockcomm [$i + $j], 'fs_13 uppercase mt_10') !==false) {$flag = 1;};
            } while ($flag == 0);
            $comm [$k]['text'] = mb_substr($comm [$k]['text'],12);

        }

        if (strpos($blockcomm [$i], 'fs_13 uppercase mt_10') !== false)
        {
            $comm [$k]['time'] = iconv('windows-1251', 'utf-8', strip_tags($blockcomm [$i]));
            $comm [$k]['time'] = patch_time(mb_substr($comm [$k]['time'],12));
            $comm [$k]['url'] = $line['url'];
            $comm [$k]['header'] = $line['header'].' | <b>'.$time.'</b>';
            $k++;
        }
    }
    //var_dump($comm);die();
    return $comm;
}

function patch_time ($str)
{
    if (mb_strlen($str)<6) //значит что день совпадает с сегодня
    {
        $date = date('d-m-Y');
        return $date.' '.$str;
    }
    else
    {
        $t1 = explode (" ",$str);
        $time = $t1[0];
        $day = $t1[1];
        $month_ru = $t1[2];
        $month = name_month_to_num($month_ru);
        
        $today = get_today();
        $year = $today['year'];

        if ( ($today['day']==1) && ($today['month_n']==1) && ($month==12) && ($day>26) )
        {
            $year = $year-1;
        }
        elseif ( ($today['day']==2) && ($today['month_n']==1) && ($month==12) && ($day>27) )
        {
            $year = $year-1;
        }
        elseif ( ($today['day']==3) && ($today['month_n']==1) && ($month==12) && ($day>28) )
        {
            $year = $year-1;
        }
        elseif ( ($today['day']==4) && ($today['month_n']==1) && ($month==12) && ($day>29) )
        {
            $year = $year-1;
        }
        elseif ( ($today['day']==5) && ($today['month_n']==1) && ($month==12) && ($day>30) )
        {
            $year = $year-1;
        }

        // присовываем нолики к дням и месяцам если надо
        if ($day<10) $day = '0'.$day;
        if ($month<10) $month = '0'.$month;

        $fulldate = $day.'-'.$month.'-'.$year.' '.$time;

        return $fulldate;
    }
}