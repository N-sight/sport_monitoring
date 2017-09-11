<?php

class Sports_crawler
{
    public static function get_news($mon_day, $finish_day)
    {

        $start_day = $mon_day['old'];
        $stop_day = $finish_day['old'];

        if ($start_day[0] == '0') $start_day = mb_substr($start_day, 1);
        if ($stop_day[0] == '0') $stop_day = mb_substr($stop_day, 1);

        $search_start = '<b>' . $start_day; // формируем строку начала поиска
        $search_end = '<b>' . $stop_day; // формируем строку конца поиска

        $on_load_content = 0; // флаг загрузки.
        $flag_start = 0;
        $flag_end = 0;

        $page_num = 0;
        $url_page = array(); // массив с URL страниц.
        $url_page [$page_num] = 'http://www.sports.ru/news/football/';


        $data = array(); // массив с загруженными страницами
        $unsorted_news = array(); // место сбора
        $n = 0; // свободный счётчик
        $k = 0; // счетчик защиты от перегрузок к обращениям к серверу
        do {
            //  сбор всего , что относится новостям за вчера.
            $k++;

            $data [$page_num] = file_get_contents($url_page [$page_num]);
            //echo 'Заходим на страницу : '.$page_num. ' - '.$url_page [$page_num].'<br>';

            $rows = explode("\n", $data [$page_num]);
            $page_news = self::getpage($rows); // вырезаем весь HTML находящийся в поле редакционных новостей.

            // проверка и комплектация
            for ($i = 0; $i < count($page_news); $i++) {
                if (strpos($page_news[$i], $search_start) !== false) // находим заголовок с надписью "вчера"
                {
                    $flag_start = 1;
                    $on_load_content = 1;
                }

                if (strpos($page_news[$i], $search_end) !== false) // находим заголовок с надписью "позавчера"
                {
                    $flag_end = 1;
                    $on_load_content = 0;
                }

                if ($on_load_content == 1) {
                    $unsorted_news [$n] = $page_news[$i];
                    $n++;
                }
            }

            $page_num++;
            $get_var = $page_num + 1;
            $url_page [$page_num] = 'http://www.sports.ru/news/football/?&page=' . $get_var; // по задумке get_var = 2 и далее.
            shortpause();
        } while ((($flag_end == 0) || ($flag_start == 0)) && ($k < 10));

        $news = self::linenews($unsorted_news);
        $result = self::format_news($news, $mon_day);

        //deb($result);
        return $result;
    }

    private static function getpage($lines) // получается вырезка текста между началом новостей и концом
    {
        //var_dump($lines);
        $news = array();
        $flag = 0; // флаг загрузки
        $n = 0; // индекс с которого начинается загрузка

        for ($i = 0; $i < count($lines); $i++) {
            if (strpos($lines [$i], '<div class="news">') !== false) // начинаем с <div class="news">
            {
                $flag = 1;
                $n = $i;
            }

            if ((strpos($lines [$i], 'Следующие 100 новостей</a>') !== false) && ($i > 800)) //заканчиваем этим
            {
                return $news;
            }

            if ($flag == 1) {
                $news[$i - $n] = $lines[$i];
            }
        }
        return false;
    }

    private static function linenews($lines)
    {
        $news = array();
        $n = 0; // индекс массива с новостями.

        for ($i = 0; $i < count($lines); $i++) {
            if (strpos($lines [$i], 'span class="time"') !== false) // хватаем новость
            {
                $time = mb_substr($lines[$i], 55, 5); //время новости

                for ($k = 1; $k < 5; $k++) {
                    if (strpos($lines [$i + $k], 'href') !== false) // поймали строку с надписью href
                    {
                        $n1 = (int)mb_strpos($lines [$i + $k], 'href');
                        $n2 = (int)mb_strpos($lines [$i + $k], 'html');
                        $n3 = $n2 - $n1 + 5;
                        $news_url = mb_substr($lines[$i + $k], $n1, $n3);

                        $news_header = strip_tags($lines[$i + $k]);

                        $news [$n] = $time . " " . $news_url . " " . $news_header;
                        $n++;
                    }
                }
            }
        }
        return $news;
    } // сортировщик. получается вырезка из грязного HTML

    private static function format_news($lines, $day)
    {
        //deb($lines);
        $result = array();

        for ($i = 0; $i < count($lines); $i++) {
            $time = mb_substr($lines[$i], 0, 5);
            $result[$i]['date'] = $day['date'] . ' ' . $time;

            $n2 = (int)mb_strpos($lines [$i], 'html');
            $news_header = explode('|', trim(mb_substr($lines[$i], $n2 + 5)));
            $result[$i]['header'] = $news_header[0];

            //writeln($i);
            //$url = get_HTML_parameter($lines[$i],"href");
            $url = get_between($lines[$i], 'href="', 'html"');
            $url = $url . 'html';
            if (contains($url, "http")) {
                unset($result[$i]);
                continue;
            }
            $news_url = 'http://www.sports.ru' . $url;
            $result[$i]['url'] = $news_url;

            $save_string = '<a target="_blank" href="' . $news_url . '">' . $news_header[0] . '</a>';
            $result[$i]['ss'] = $save_string;

            $n1 = (int)mb_strpos($lines [$i], '|');
            $last = mb_substr($lines[$i], $n1 + 1);
            $cut = mb_strpos($last, '|');
            if ($cut !== false) {
                $last = mb_substr($last, 0, $cut);
            }
            $result[$i]['comm'] = (int)trim($last);

        }

        rsort($result);

        return $result;
    }
}