<?php

class Sportexpress_crawler
{

    public static function get_news($mon_day, $finish_day)
    {
        $start_day = $mon_day['old'];
        $stop_day = $finish_day['old'];

        if ($start_day[0] == '0') $start_day = mb_substr($start_day, 1);
        if ($stop_day[0] == '0') $stop_day = mb_substr($stop_day, 1);
        $today = get_today();
        $today = $today['old'];
        if ($today[0] == '0') $today = mb_substr($today, 1);

        $n = 0; // счетчик новостей конечновыделенных новостей
        $k = 0; // cчетчик внутристраничный
        $result = array(); // массив куда попадут новости нужные новости за день.
        $data = array(); // массив сбора всех страниц , что выдает http сервер.
        $rows = array(); // разбитый на строчки массив data
        $blocknews = array(); //массив rows , разбитый на
        $allnews = array(); // массив всех новостей, что обнаружили на входной странице
        $flag = 0;  // флаг краулера flag = 0 - крутим, 1 - заканчиваем
        $flag_page = 0; // флаг странички флаг краулера flag_page = 0 - не грузим, 1 - грузим
        $num = 0; // счетчик страниц краулера
        $url = ''; // переменная в которой будет содержатся урл страницы краулера.

        do {
            shortpause();
            $num++;
            if ($num == 1) {
                $url = 'http://www.sport-express.ru/football/news/';
            } else {
                $url = 'http://www.sport-express.ru/football/news/page' . $num . '/';
            }
            //echo 'Заходим на страничку: num = '.$num.' : '.$url.'<br>';


            // блок сбора всех новостей со страницы
            $data [$num] = file_get_contents($url);
            $rows = explode("\n", $data [$num]); // разбираем чехарду на строчки

            // тут выделяем  html код новостного блока без разбора нужно/не нужно
            for ($i = 0; $i < count($rows); $i++) {
                if (strpos($rows [$i], '<div class="mt_10 clearfix materials_container">') !== false) // Точка начала скачивания новостей
                {
                    $flag_page = 1;
                }

                if (strpos($rows [$i], '<div class="clearfix mt_20 materials_container hidden">') !== false) //заканчиваем этим
                {
                    $flag_page = 0; // перестаем грузить
                }

                if ($flag_page == 1) {
                    $blocknews[$k] = $rows[$i];
                    $k++;
                }
            }
            // на выходе из цикла получаем массив html кода внутри новостного блока $blocknewsz
            //var_dump($blocknews);

            $k = 0; // обнуляем счетчик заголовков новостей
            //разбираем код блока новостей на индивидуальные новости.
            for ($i = 0; $i < count($blocknews); $i++) {
                if (strpos($blocknews [$i], '<div class="ml_20">') !== false) // время новости и вход в нее
                {
                    $str = iconv('windows-1251', 'utf-8', $blocknews[$i + 3]);
                    $n1 = mb_strpos($str, '_gray">') + 7;
                    $n2 = mb_strpos($str, '</span>');
                    $allnews[$k]['date'] = mb_substr($str, $n1, $n2 - $n1);

                    // если новость найдена, то защищаем ее от ошибок
                    if ($allnews[$k]['date'] == '') {
                        echo "Error on page $num <br>";
                    }
                    $allnews[$k]['comm'] = 0;
                }

                if ((strpos($blocknews [$i], '<a class="f_left"') !== false) && (isset($allnews[$k]['date'])))// количество комментариев к новости
                {
                    $str = iconv('windows-1251', 'utf-8', $blocknews[$i]);
                    $n1 = mb_strpos($str, '_form">') + 7;
                    $n2 = mb_strpos($str, '</a');
                    $allnews[$k]['comm'] = (int)mb_substr($str, $n1, $n2 - $n1);;
                }

                if ((strpos($blocknews [$i], '<a class="fs_20 mb_10 block black') !== false) && (isset($allnews[$k]['date']))) {
                    $str = iconv('windows-1251', 'utf-8', $blocknews[$i]);
                    $allnews[$k]['header'] = mb_substr(strip_tags($str), 12);

                    if (strpos($blocknews [$i], '</a>') == false) // заплатка , но и такое не часто бывает.
                    {
                        $allnews[$k]['header'] = $allnews[$k]['header'] . iconv('windows-1251', 'utf-8', $blocknews [$i + 1]);
                    }

                    $n1 = mb_strpos($str, 'href="') + 6;
                    $str2 = mb_substr($str, $n1);
                    $n2 = mb_strpos($str2, '">');
                    $allnews[$k]['url'] = mb_substr($str2, 0, $n2);

                    $k++;
                }
            }


            // отбираем новости по дню мониторинга
            for ($i = 0; $i < count($allnews); $i++) {
                if (($today == $start_day) && (mb_strlen($allnews [$i]['date']) < 6)) {
                    $result[$n] = $allnews [$i];
                    $n++;
                    continue;
                }

                if (mb_strpos($allnews [$i]['date'], $start_day) !== false) {
                    $result[$n] = $allnews [$i];
                    $n++;
                    continue;
                }

                if (mb_strpos($allnews [$i]['date'], $stop_day) !== false) {
                    $flag = 1;
                }

            }

        } while ($flag !== 1);

        for ($i = 0; $i < count($result); $i++) {
            if (mb_strlen($result[$i]['date']) < 6) {
                $result[$i]['date'] = $mon_day['date'] . ' ' . $result[$i]['date'];
            } else {
                $result[$i]['date'] = $mon_day['date'] . ' ' . mb_substr($result[$i]['date'], 0, mb_strpos($result[$i]['date'], ' '));
            }
        }


        return $result;
    }
    
}
