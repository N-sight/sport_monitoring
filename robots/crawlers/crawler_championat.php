<?php

class Championat_crawler
{

    public static function get_news($mon_day, $finish_day)
    {

        $start_day = $mon_day['old'];
        $stop_day = $finish_day['old'];


        if ($start_day[0] == '0') $start_day = mb_substr($start_day, 1);
        if ($stop_day[0] == '0') $stop_day = mb_substr($stop_day, 1);
        $search_start = '<div class="list-articles__day">' . $start_day; // формируем строку начала поиска
        $search_end = '<div class="list-articles__day">' . $stop_day; // формируем строку конца поиска

        $on_load_content = 0; // флаг загрузки.
        $flag_start = 0;
        $flag_end = 0;

        $page_num = 0;
        $url_page = array(); // массив с URL страниц.
        $url_page [$page_num] = 'http://www.championat.com/news/football/1.html';

        $data = array(); // массив с загруженными страницами
        $unsorted_news = array(); // место сбора
        $n = 0; // свободный счётчик
        $i = 0; // свободный счётчик
        $p = 0; // свободный счётчик

        do {
            $p++;
            //  сбор всего, что относится новостям за вчера.
            $data [$page_num] = file_get_contents($url_page [$page_num]);

            //echo 'Заходим на страницу : '.$page_num.' - '.$url_page [$page_num];

            $rows = explode("\n", $data [$page_num]);
            $page_news = self::getpage_champion($rows); // вырезаем весь HTML находящийся в поле редакционных новостей.

            for ($i = 0; $i < count($page_news); $i++) {
                if (strpos($page_news[$i], $search_start) !== false) // находим заголовок с надписью "вчера" и начинаем отбирать
                {
                    $flag_start = 1;
                    $on_load_content = 1;
                }

                if (strpos($page_news[$i], $search_end) !== false) // находим заголовок с надписью "позавчера" и заканчиваем отбирать
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
            $url_page [$page_num] = 'http://www.championat.com/news/football/' . $get_var . '.html'; // по задумке get_var = 2 и далее.
            shortpause();
            //echo ' ... слили<br>';

            if ($p > 20) {
                writeln('обошли больше 20 страниц и не нашли нужного дня');
                die();
            }  // Защита от
        } while (($flag_end == 0) || ($flag_start == 0));


        //echo "... забрали все, ок<br>";
        $result = self::linenews_champion($unsorted_news, $mon_day);
        //echo "... отсортировали , ок<br>";
        //var_dump($result);

        // тут блок выхода на api рамблера за комментариями.

        $str = '';
        $k = 0;

        for ($i = 0; $i < count($result); $i++) {
            $str = $str . '&xid=' . $result[$i]['data-id'];
            if ((($i + 1) % 50 == 0) && ($i !== 0) || ($i == count($result) - 1)) {
                $link[$k] = 'https://c.rambler.ru/api/app/5/comments-count?' . mb_substr($str, 1);
                //https://c.rambler.ru/api/app/5/comments-count?xid=news_2860272&xid=news_2860248&xid=news_2860186&xid=news_2860166&xid=news_2860146&xid=news_2859756&xid=news_2859730&xid=news_2859868&xid=news_2859946&xid=news_2859540&xid=news_2860092&xid=news_2860282&xid=news_2860272&xid=news_2860260&xid=news_2860258&xid=news_2860254&xid=news_2860250&xid=news_2860248&xid=news_2860240&xid=news_2860222&xid=news_2860214&xid=news_2860208&xid=news_2860204&xid=news_2860198&xid=news_2860192&xid=news_2860186&xid=news_2860182&xid=news_2860178&xid=news_2860170&xid=news_2860168&xid=news_2860166&xid=news_2860164&xid=news_2860162&xid=news_2860160&xid=news_2860158&xid=news_2860156&xid=news_2860154&xid=news_2860152&xid=news_2860150&xid=news_2860146&xid=news_2860144&xid=news_2860140&xid=news_2860138&xid=news_2860136&xid=news_2860134&xid=news_2860132&xid=news_2860128&xid=news_2860126&xid=news_2860124&xid=news_2860122&xid=news_2860120&xid=news_2860118&xid=news_2860112&xid=news_2860110&xid=news_2860108&xid=news_2860106&_=1500970860799
                //echo $link[$k].'<br>';
                $keys[$k] = json_decode(file_get_contents($link[$k]));

                $str = "";
                $k++;
            }
        }

        //var_dump($keys);die;

        // todo очень дурацкий момент
        for ($i = 0; $i < count($keys); $i++) {
            $o1 = $keys[$i];
            $o2 = $o1->xids;

            foreach ($o2 as $k1 => $value) {
                //echo $k1.'=>'.$value.'<br>';
                for ($k = 0; $k < count($result); $k++) {
                    if ($result [$k]['data-id'] == $k1) {
                        $result [$k]['comm'] = $value;
                        //echo $k.'='.$value.'<br>';
                    }
                }
            }
        }

        return $result;
    }

    private static function getpage_champion($lines) // получается вырезка текста между началом новостей и концом
    {
        $news = array();
        $flag = 0; // флаг загрузки
        $n = 0; // индекс с которого начинается загрузка

        for ($i = 0; $i < count($lines); $i++) {
            //if (strpos($lines [$i], '<div class="list-articles">') !== false) // Точка начала скачивания новостей
            if (strpos($lines [$i], 'id="news_list"') !== false) // Точка начала скачивания новостей
            {
                $flag = 1;
                $n = $i;
            }

            //if (strpos($lines [$i], '<div class="paging">') !== false) //заканчиваем этим
            if (strpos($lines [$i], 'paging__contain') !== false) //заканчиваем этим
            {
                return $news;
            }


            if ($flag == 1) {
                $news[$i - $n] = $lines[$i];
            }
        }

        return false;
    }

    private static function linenews_champion($lines, $date) // $lines - массив html кода собранный по дню мониторинга. результат - массив строк со всеми новостями и ссылками
    {
        $news = array();
        $n = 0; // индекс массива с новостями.

        for ($i = 0; $i < count($lines); $i++) {
            if (strpos($lines [$i], '<div class="list-articles__i">') !== false) // тут начало каждой новости
            {
                $news [$n]['date'] = mb_substr($lines[$i + 1], mb_strpos($lines[$i + 1], '>') + 1, 5); //время новости
                $news [$n]['date'] = $date['date'] . ' ' . $news [$n]['date'];
                $news [$n]['header'] = mb_substr($lines[$i + 4], mb_strpos($lines[$i + 4], 'label=') + 7, -1);
                $news [$n]['url'] = 'http://www.championat.com' . mb_substr($lines[$i + 2], mb_strpos($lines[$i + 2], 'href=') + 6, (mb_strpos($lines[$i + 2], ' target') - mb_strpos($lines[$i + 2], 'href=') - 7));
                $news [$n]['comm'] = 0;

                for ($j = 4; $j < 12; $j++) {
                    if (strpos($lines [$i + $j], 'data-id') !== false) {
                        $news [$n]['data-id'] = mb_substr($lines[$i + $j], mb_strpos($lines[$i + $j], 'data-id="') + 9, 12);
                        break;
                    }
                }

                if (!isset($news [$n]['data-id'])) {
                    var_dump($lines);
                    var_dump($i);
                    die;
                } // контролька

                $n++;
            }
        }
        return $news;
    } // сортировщик. получается вырезка из грязного HTML

}