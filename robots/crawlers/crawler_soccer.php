<?php

class Soccer_crawler
{
    public static function get_news($mon_day, $finish_day)
    {
        $start_day_marker = $mon_day['old'];
        $stop_day_marker = $finish_day['old'];

        $num = 0; // счетчик страниц краулера
        $url = ''; // переменная в которой будет содержатся урл страницы краулера.
        $rows = array(); // массив сбора всех страниц , что выдает http сервер.

        $today = get_today();
        $today = $today['old'];
        $yesterday = get_yesterday();
        $yesterday = $yesterday['old'];

        $unsorted_news = array(); // строчки разметки по конкретному дню
        $k = 0; // счетчик строчек разметки $unsorted_news
        $on_load_day = 0; // флаг загрузки строчек по дням
        $flag = 0;  // флаг краулера flag = 0 - крутим, 1 - заканчиваем (Перелистывать страницы).

        $flow = array(); // выходной массив с новостями.

        do {
            shortpause();
            $num++;
            $n = 0; // счетчик строчек html разметки несортированных новостей $unsorted_lines, чтобы каждый раз итерация с $unsorted_lines была новой
            $unsorted_lines = array(); // место сбора новостей с каждой странички
            $on_load_lines = 0; // флаг отбора строчек по страницам

            if ($num == 1) {
                $url = 'https://www.soccer0010.com/news';
            } else {
                $url = 'https://www.soccer0010.com/news?page=' . ($num - 1);
            }

            // блок сбора со страницы
            $rows [$num] = explode("\n", file_get_contents($url)); // разбираем чехарду на строчки

            // тут выделяем  html код новостного блока без разбора нужно/не нужно
            for ($i = 0; $i < count($rows[$num]); $i++) {
                if (strpos($rows [$num][$i], '<div class="view-content">') !== false) // Точка начала скачивания новостей
                {
                    $on_load_lines = 1;
                }
                if (strpos($rows [$num][$i], '<div class="site-pager">') !== false) // Точка конца скачивания новостей
                {
                    $on_load_lines = 0;
                }
                if ($on_load_lines == 1) {
                    $unsorted_lines[$n] = $rows [$num][$i];
                    $n++;
                }
            }

            //var_dump($num);
            //var_dump($unsorted_lines); // разметка новостей привязанная к странице

            if (($today == $start_day_marker) && ($num == 1)) // для первой страницы обеспечен старт загрузки сразу
            {
                $on_load_day = 1;
            }

            if ($start_day_marker == $yesterday) $start_day_marker = 'Вчера';
            if ($stop_day_marker == $yesterday) $stop_day_marker = 'Вчера';

            for ($i = 0; $i < count($unsorted_lines); $i++) {
                if (strpos($unsorted_lines[$i], ($start_day_marker . '</div>')) !== false) // Точка начала скачивания новостей
                {
                    $on_load_day = 1;
                }

                if (strpos($unsorted_lines[$i], ('>' . $stop_day_marker . '</div>')) !== false) // Точка начала скачивания новостей
                {
                    $flag = 1;
                    break;
                }

                if ($on_load_day == 1) {
                    $unsorted_news[$k] = $unsorted_lines[$i];
                    $k++;
                }
            }

        } while (($flag !== 1) && ($num <= 25)); //25 страница обеспечивает правило 5 дней в прошлое.

        unset($unsorted_lines);
        unset($rows);
        $n = 0;

        for ($i = 0; $i < count($unsorted_news); $i++) {
            if (contains($unsorted_news[$i], '<div class="news'))
            {
                $flow[$n]['date'] = $mon_day['date'] . ' ' . cut_leftspaces(strip_tags($unsorted_news[$i + 1]));
                $flow[$n]['url'] = 'https://www.soccer0010.com' . mb_substr($unsorted_news[$i + 3], 33, 19);

                $left2 = mb_substr($unsorted_news[$i + 3], 0, mb_strpos($unsorted_news[$i + 3], '</a>')+4);
                $flow[$n]['header'] =strip_tags($left2);

                $right = get_between($unsorted_news[$i+3],'.png"/>','</span></a>');
                $flow[$n]['comm'] = (int) $right;
                $n++;
            }

        }
        
        return $flow;
    }
}
