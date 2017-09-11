<?php

class Sovsport_crawler
{
    public static function get_news ($mon_day, $finish_day)
    {
        $start_day = $mon_day['old'];
        $stop_day = $finish_day['old'];

        $result = array();
        $news = array(); // промежуточный массив всех новостей за этот нужный день

        $flag = 0; // 0 - работаем , 1 выходим из функции сбора заголовков
        $n = 0; // сквозная переменная учета всех новостей с блока загрузки

        $jsurl = 'http://www.sovsport.ru/json_346_347/football/';
        $data = file_get_contents($jsurl);
        $allnews = json_decode($data);
        $id_name = '@raw';
        $strmon = ''; // описание месяца словами
        $strmonf = '';// описание месяца словами
        $strmonv = '';// описание месяца словами
        $day = '';
        $month = '';
        $lastid = '';

        // БЛОК РАБОТЫ С JSON1
        // первый блок информации из json
        for ($i = 0; $i < count($allnews[0]->content); $i++) {
            $news[$i]['date'] = $allnews[0]->content[$i]->pubdate;
            $news[$i]['header'] = $allnews[0]->content[$i]->heading;
            $news[$i]['url'] = 'http://www.sovsport.ru/news/text-item/' . $allnews[0]->content[$i]->content_id;
            $news[$i]['data-id'] = $allnews[0]->content[$i]->stamp->$id_name;
            $news[$i]['comm'] = (int)$allnews[0]->content[$i]->comment_count;
            $news[$i]['type'] = 'lefttext1';

            $day = mb_substr($news[$i]['date'], 5, 2);
            $month = mb_substr($news[$i]['date'], 8, 3);

            switch ($month) {
                case 'Jan':
                    $strmon = 'января';
                    break;
                case 'Feb':
                    $strmon = 'февраля';
                    break;
                case 'Mar':
                    $strmon = 'марта';
                    break;
                case 'Apr':
                    $strmon = 'апреля';
                    break;
                case 'May':
                    $strmon = 'мая';
                    break;
                case 'Jun':
                    $strmon = 'июня';
                    break;
                case 'Jul':
                    $strmon = 'июля';
                    break;
                case 'Aug':
                    $strmon = 'августа';
                    break;
                case 'Sep':
                    $strmon = 'сентября';
                    break;
                case 'Oct':
                    $strmon = 'октября';
                    break;
                case 'Nov':
                    $strmon = 'ноября';
                    break;
                case 'Dec':
                    $strmon = 'декабря';
                    break;
            }
            $news[$i]['date_check'] = $day . ' ' . $strmon;
            $lastid = $news[$i]['data-id'];
        }

        $k = 0;

        // БЛОК РАБОТЫ С JSON2
        // второй блок информации из json
        do {
            $k++;
            $n = $n + $i;
            $jsurl = 'http://www.sovsport.ru/js/content_by_sport_or_match_in_json_2_50_d' . $lastid . '_0_0_sport_295_498_466_465_308_307_306_304_303_302_301_300_299_298_448_432_431_297_296_294_293_292_291_290_289_288_287_1.json';
            $data = file_get_contents($jsurl);
            $allnews = json_decode($data);

            for ($i = 0; $i < count($allnews); $i++) {
                $news[$i + $n]['date'] = $allnews[$i]->pubdate;
                $news[$i + $n]['header'] = $allnews[$i]->heading;
                $news[$i + $n]['url'] = 'http://www.sovsport.ru/news/text-item/' . $allnews[$i]->content_id;
                $news[$i + $n]['comm'] = (int)$allnews[$i]->comment_count;
                $news[$i + $n]['type'] = 'lefttext2';
                $s1 = $allnews[$i]->stamp;
                $s2 = str_replace('.', '', $s1);
                $s3 = str_replace(' ', '', $s2);
                $s4 = str_replace('-', '', $s3);
                $s5 = str_replace(':', '', $s4);

                $news[$i + $n]['data-id'] = $s5;


                $day = mb_substr($news[$i + $n]['date'], 5, 2);
                $month = mb_substr($news[$i + $n]['date'], 8, 3);

                switch ($month) {
                    case 'Jan':
                        $strmon = 'января';
                        break;
                    case 'Feb':
                        $strmon = 'февраля';
                        break;
                    case 'Mar':
                        $strmon = 'марта';
                        break;
                    case 'Apr':
                        $strmon = 'апреля';
                        break;
                    case 'May':
                        $strmon = 'мая';
                        break;
                    case 'Jun':
                        $strmon = 'июня';
                        break;
                    case 'Jul':
                        $strmon = 'июля';
                        break;
                    case 'Aug':
                        $strmon = 'августа';
                        break;
                    case 'Sep':
                        $strmon = 'сентября';
                        break;
                    case 'Oct':
                        $strmon = 'октября';
                        break;
                    case 'Nov':
                        $strmon = 'ноября';
                        break;
                    case 'Dec':
                        $strmon = 'декабря';
                        break;
                }
                $news[$i + $n]['date_check'] = $day . ' ' . $strmon;
                $lastid = $news[$i + $n]['data-id'];
            }

            shortpause();

        } while (($news[$i + $n - 1]['date_check'] !== $stop_day) && ($k < 10)); // $k - защита

        //var_dump($news);die;

        // переливаем в результирующий массив информацию из json
        $n = 0; // отберем еще разок по новостям по дню.
        for ($i = 0; $i < count($news); $i++) // переливаем в результирующий массив
        {
            if ($news[$i]['date_check'] == $start_day) {
                $result[$n] = $news[$i];
                $n++; // СЧЕТЧИК $n НЕ трогаем!!!.
            }
        }
        //var_dump($result);var_dump($start_day);die();

        // БЛОК РАБОТЫ С ФОТОНОВОСТЯМИ , третий блок получения информации
        // забираем с главной страницы фотоновости // новости идут непоследовательно, и не все отображаются.
        $jsurl = 'http://www.sovsport.ru/js/content_by_sport_or_match_in_json_3_10_0_0_0_sport_295_498_466_465_308_307_306_304_303_302_301_300_299_298_448_432_431_297_296_294_293_292_291_290_289_288_287_1.json';
        $data = file_get_contents($jsurl);
        $fotonews = json_decode($data);

        for ($i = 0; $i < count($fotonews); $i++) {
            $fnews[$i]['date'] = mb_substr($fotonews[$i]->created, 0, 16);
            $fnews[$i]['header'] = $fotonews[$i]->content;
            $fnews[$i]['url'] = 'http://www.sovsport.ru/photo/gallery-item/' . $fotonews[$i]->gallery_id;
            $fnews[$i]['comm'] = (int)$fotonews[$i]->comment_count;
            $fnews[$i]['type'] = 'fotonews';
            $s1 = $fotonews[$i]->created;
            $s2 = str_replace('.', '', $s1);
            $s3 = str_replace(' ', '', $s2);
            $s4 = str_replace('-', '', $s3);
            $s5 = str_replace(':', '', $s4);
            $fnews[$i]['data-id'] = $s5;

            $day = mb_substr($fnews[$i]['data-id'], 6, 2);
            $month = mb_substr($fnews[$i]['data-id'], 4, 2);
            switch ($month) {
                case '01':
                    $strmonf = 'января';
                    break;
                case '02':
                    $strmonf = 'февраля';
                    break;
                case '03':
                    $strmonf = 'марта';
                    break;
                case '04':
                    $strmonf = 'апреля';
                    break;
                case '05':
                    $strmonf = 'мая';
                    break;
                case '06':
                    $strmonf = 'июня';
                    break;
                case '07':
                    $strmonf = 'июля';
                    break;
                case '08':
                    $strmonf = 'августа';
                    break;
                case '09':
                    $strmonf = 'сентября';
                    break;
                case '10':
                    $strmonf = 'октября';
                    break;
                case '11':
                    $strmonf = 'ноября';
                    break;
                case '12':
                    $strmonf = 'декабря';
                    break;
            }
            $fnews[$i]['date_check'] = $day . ' ' . $strmonf;

            if ($fnews[$i]['date_check'] == $start_day) {
                $result[$n] = $fnews[$i];
                $result[$n]['must'] = 1; // новости с флажком маст при наличии комментариев просматривать все
                $n++;
            }
        }
        // БЛОК РАБОТЫ С Видео НОВОСТЯМИ
        // забираем с главной страницы видеоновости, четвертый блок получения информации

        $jsurl = 'http://www.sovsport.ru/js/content_by_sport_or_match_in_json_6_10_0_0_0_sport_295_498_466_465_308_307_306_304_303_302_301_300_299_298_448_432_431_297_296_294_293_292_291_290_289_288_287_1.json';
        $data = file_get_contents($jsurl);
        $videonews = json_decode($data);

        //var_dump($videonews); die();

        for ($i = 0; $i < count($videonews); $i++) {
            $content_id = mb_substr($videonews[$i]->preview_160_120, mb_strpos($videonews[$i]->preview_160_120, '160/') + 4, mb_strpos($videonews[$i]->preview_160_120, '.jpg') - mb_strpos($videonews[$i]->preview_160_120, '160/') - 4);
            $vnews[$i]['date'] = mb_substr($videonews[$i]->stamp, 0, 16);
            $vnews[$i]['header'] = $videonews[$i]->heading;
            $vnews[$i]['url'] = 'http://www.sovsport.ru/video/gallery-item/' . $content_id;
            $vnews[$i]['comm'] = (int)$videonews[$i]->comment_count;
            $vnews[$i]['type'] = 'videonews';
            $s1 = $videonews[$i]->stamp;
            $s2 = str_replace('.', '', $s1);
            $s3 = str_replace(' ', '', $s2);
            $s4 = str_replace('-', '', $s3);
            $s5 = str_replace(':', '', $s4);
            $vnews[$i]['data-id'] = $s5;

            $day = mb_substr($vnews[$i]['date'], 8, 2);
            $month = mb_substr($vnews[$i]['date'], 5, 2);
            switch ($month) {
                case '01':
                    $strmonv = 'января';
                    break;
                case '02':
                    $strmonv = 'февраля';
                    break;
                case '03':
                    $strmonv = 'марта';
                    break;
                case '04':
                    $strmonv = 'апреля';
                    break;
                case '05':
                    $strmonv = 'мая';
                    break;
                case '06':
                    $strmonv = 'июня';
                    break;
                case '07':
                    $strmonv = 'июля';
                    break;
                case '08':
                    $strmonv = 'августа';
                    break;
                case '09':
                    $strmonv = 'сентября';
                    break;
                case '10':
                    $strmonv = 'октября';
                    break;
                case '11':
                    $strmonv = 'ноября';
                    break;
                case '12':
                    $strmonv = 'декабря';
                    break;
            }
            $vnews[$i]['date_check'] = $day . ' ' . $strmonv;

            if ($vnews[$i]['date_check'] == $start_day) {
                $result[$n] = $vnews[$i];
                $result[$n]['must'] = 1; // новости с флажком маст при наличии комментариев просматривать все
                $n++;
            }
        }


        // БЛОК РАБОТЫ С ПРЯМЫМИ ДАННЫМИ
        // пятый поток данных - то что отдает непосресдственно http сервер
        $url = 'http://www.sovsport.ru/football/';
        $data = file_get_contents($url);
        $rows = explode("\n", $data);
        $k = 0; //счетчик новостей на главной странице
        for ($i = 0; $i < count($rows); $i++) {
            if (strpos($rows[$i], 'other-articles_item_photo_frame') !== false) {
                $topnews[$k]['header'] = mb_substr($rows[$i], mb_strpos($rows[$i], 'alt="') + 5, mb_strpos($rows[$i], '"></a>') - mb_strpos($rows[$i], 'alt="') - 5);
                $topnews[$k]['date'] = mb_substr($rows[$i - 1], mb_strpos($rows[$i - 1], 'stamp="') + 7, 14);
                $topnews[$k]['url'] = 'http://www.sovsport.ru' . mb_substr($rows[$i], mb_strpos($rows[$i], 'href="') + 6, mb_strpos($rows[$i], '"><img class') - mb_strpos($rows[$i], 'href="') - 6);
                $topnews[$k]['comm'] = '404';
                $topnews[$k]['type'] = 'topnewsHTTP';

                $lastid = $topnews[$k]['date'];
                $day = mb_substr($topnews[$k]['date'], 6, 2);
                $month = mb_substr($topnews[$k]['date'], 4, 2);
                switch ($month) {
                    case '01':
                        $strmonv = 'января';
                        break;
                    case '02':
                        $strmonv = 'февраля';
                        break;
                    case '03':
                        $strmonv = 'марта';
                        break;
                    case '04':
                        $strmonv = 'апреля';
                        break;
                    case '05':
                        $strmonv = 'мая';
                        break;
                    case '06':
                        $strmonv = 'июня';
                        break;
                    case '07':
                        $strmonv = 'июля';
                        break;
                    case '08':
                        $strmonv = 'августа';
                        break;
                    case '09':
                        $strmonv = 'сентября';
                        break;
                    case '10':
                        $strmonv = 'октября';
                        break;
                    case '11':
                        $strmonv = 'ноября';
                        break;
                    case '12':
                        $strmonv = 'декабря';
                        break;
                }
                $topnews[$k]['date_check'] = $day . ' ' . $strmonv;

                // проверка на адривер
                if ((strpos($topnews[$k]['url'], 'adriver') !== false) || (strpos($topnews[$k]['url'], 'adfox') !== false)) {
                    unset ($topnews[$k]);
                    $k--;
                }

                //присоединение к основному блоку новостей
                if (isset ($topnews[$k]['date_check'])) {
                    if ($topnews[$k]['date_check'] == $start_day) {
                        $result[$n] = $topnews[$k];
                        $result[$n]['must'] = 1; // новости с флажком маст при наличии комментариев просматривать все
                        $n++;
                    }
                }
                $k++;
            }
        }
        //var_dump($topnews);die;

        // БЛОК РАБОТЫ С ГЛАВНОЙ СТРАНИЦЫ
        //последний блок новостей с главной страницы

        $k = 0; // защитный счётчик от перегрузок обращения к серверу.
        $j = 0;
        $i = 0; // промежуточный сквозной счётчик
        $flag_stop = 0;
        do {
            $k++;
            $j = $j + $i;
            $jsurl = 'http://www.sovsport.ru/js/content_by_sport_or_match_in_json_5_24_d' . $lastid . '_0_0_sport_295_498_466_465_308_307_306_304_303_302_301_300_299_298_448_432_431_297_296_294_293_292_291_290_289_288_287_1.json';
            $data = file_get_contents($jsurl);
            $atlist = json_decode($data);

            for ($i = 0; $i < count($atlist); $i++) {
                $addtopnews[$i + $j]['date'] = $atlist[$i]->pubdate;
                $addtopnews[$i + $j]['header'] = $atlist[$i]->heading;
                $addtopnews[$i + $j]['url'] = 'http://www.sovsport.ru' . $atlist[$i]->link . $atlist[$i]->content_id;
                $addtopnews[$i + $j]['comm'] = (int)$atlist[$i]->comment_count;
                $addtopnews[$i + $j]['type'] = 'topnewsJS';
                $s1 = $atlist[$i]->stamp;
                $s2 = str_replace('.', '', $s1);
                $s3 = str_replace(' ', '', $s2);
                $s4 = str_replace('-', '', $s3);
                $s5 = str_replace(':', '', $s4);

                $addtopnews[$i + $j]['data-id'] = $s5;


                $day = mb_substr($addtopnews[$i + $j]['date'], 5, 2);
                $month = mb_substr($addtopnews[$i + $j]['date'], 8, 3);

                switch ($month) {
                    case '01':
                        $strmon = 'января';
                        break;
                    case '02':
                        $strmon = 'февраля';
                        break;
                    case '03':
                        $strmon = 'марта';
                        break;
                    case '04':
                        $strmon = 'апреля';
                        break;
                    case '05':
                        $strmon = 'мая';
                        break;
                    case '06':
                        $strmon = 'июня';
                        break;
                    case '07':
                        $strmon = 'июля';
                        break;
                    case '08':
                        $strmon = 'августа';
                        break;
                    case '09':
                        $strmon = 'сентября';
                        break;
                    case '10':
                        $strmon = 'октября';
                        break;
                    case '11':
                        $strmon = 'ноября';
                        break;
                    case '12':
                        $strmon = 'декабря';
                        break;
                }
                $addtopnews[$i + $j]['date_check'] = $day . ' ' . $strmon;
                $lastid = $addtopnews[$i + $j]['data-id'];
                // смотрим на флажок остановки
                if (($addtopnews[$i + $j]['date_check'] == $stop_day)) {
                    $flag_stop = 1;
                }
            }

            shortpause();

        } while (($flag_stop == 0) && ($k < 6)); // $k - защита

        //var_dump($addtopnews);die;

        for ($i = 0; $i < count($addtopnews); $i++) {
            if ($addtopnews[$i]['date_check'] == $start_day) {
                $result[$n] = $addtopnews[$i];
                $n++; // СЧЕТЧИК $n НЕ трогаем!!!.
            }
        }

        //var_dump($result);die();
        // тут чехарда с датой. исправляем Fri, 02 Jun 2017 23:56:13 +0300

        for ($i = 0; $i < count($result); $i++) {
            if (($result[$i]['type'] == 'lefttext1') || ($result[$i]['type'] == 'lefttext2') || ($result[$i]['type'] == 'topnewsJS')) {
                $t1 = mb_substr($result[$i]['date'], mb_strpos($result[$i]['date'], ',') + 2);
                $t2 = mb_substr($t1, 0, mb_strpos($t1, '+') - 1);
                $t2 = mb_substr($t2, 0, -3);
                $t3 = explode(" ", $t2);
                $result[$i]['date'] = $t3[0] . '-' . month_name_convert_shorten_tonum($t3[1]) . '-' . $t3[2] . ' ' . $t3[3];
            }
            if (($result[$i]['type'] == 'fotonews') || ($result[$i]['type'] == 'videonews')) {
                $t1 = explode(" ", $result[$i]['date']);
                $t2 = explode("-", $t1[0]);
                $result[$i]['date'] = $t2[2] . '-' . $t2[1] . '-' . $t2[0] . ' ' . $t1[1];
            }
            if (($result[$i]['type'] == 'topnewsHTTP')) {
                $hour = mb_substr($result[$i]['date'], 8, 2);
                $min = mb_substr($result[$i]['date'], 10, 2);
                $result[$i]['date'] = $mon_day['date'] . ' ' . $hour . ':' . $min;
            }

        }
        return $result;
    }
}