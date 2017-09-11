<?php

class Bobsoccer_crawler
{

    public static function get_news($mon_day, $finish_day)
    {
        $start_day = $mon_day['old'];
        $stop_day = $finish_day['old'];


        $start_day = self::upper_month($start_day);
        $stop_day = self::upper_month($stop_day);

        if ($start_day[0] == '0') $start_day = mb_substr($start_day, 1);
        if ($stop_day[0] == '0') $stop_day = mb_substr($stop_day, 1);

        $num = 0; // счетчик страниц краулера
        $url = ''; // переменная в которой будет содержатся урл страницы краулера.
        $data = array(); // массив сбора всех страниц , что выдает http сервер.
        $rows = array(); // разбитый на строчки массив data
        $blocknews = array(); //блок новостей из $rows
        $allnews = array(); // массив всех новостей, что обнаружили на входной странице
        $k = 0; // cчетчик внутристраничный
        $n = 0; // счетчик новостей конечновыделенных новостей
        $result = array(); // массив куда попадут новости нужные новости за день.
        $flag = 0;  // флаг краулера flag = 0 - крутим, 1 - заканчиваем

        do {
            shortpause();
            $num++;
            if ($num == 1) {
                $url = 'http://bobsoccer.ru/news/';
            } else {
                $url = 'http://bobsoccer.ru/news/?&part=' . $num;
            }
            //echo 'Заходим на страничку: num = '.$num.' : '.$url.'<br>';


            // блок сбора всех новостей со страницы
            $data [$num] = file_get_contents($url);
            $rows = explode("\n", $data [$num]); // разбираем чехарду на строчки

            // тут выделяем  html код новостного блока без разбора нужно/не нужно
            $blocknews = cutblock($rows, 'class="pageContent', 'class="col2');

            // на выходе из цикла получаем массив html кода внутри новостного блока $blocknews
            //var_dump($blocknews);die;

            $k = 0; // обнуляем счетчик заголовков новостей
            //разбираем код блока новостей на индивидуальные новости.
            for ($i = 0; $i < count($blocknews); $i++) {
                if (strpos($blocknews [$i], '<div class="ListElem">') !== false) //
                {
                    $str = iconv('KOI8-R', 'utf-8', $blocknews[$i + 6]);
                    $href = get_HTML_parameter($str, 'href');
                    $allnews[$k]['url'] = 'http://bobsoccer.ru' . $href;
                    $allnews[$k]['header'] = get_between($str, '<h3>', '</h3>');
                    $allnews[$k]['comm'] = (int)get_between($blocknews[$i + 3], '<div class="Counter">', '</div>');
                    $allnews[$k]['date'] = get_between($blocknews[$i + 2], 'class="icon">', '</div>');
                    $allnews[$k]['date'] = self::patch_time(iconv('KOI8-R', 'utf-8', $allnews[$k]['date']));
                    $k++;
                }

            }

            //var_dump($allnews);die;

            // отбираем новости по дню мониторинга
            for ($i = 0; $i < count($allnews); $i++) {
                if (mb_substr($allnews[$i]['date'], 0, 2) == $mon_day['day']) {
                    $result[$n] = $allnews [$i];
                    $n++;
                }
                if (mb_substr($allnews[$i]['date'], 0, 2) == $finish_day['day']) {
                    $flag = 1;
                }
            }
        } while ($flag !== 1);

        return $result;
    }

    private static function patch_time($str)
    {
        $pie = explode(" ", $str);
        $month_n = (int)name_month_to_num(mb_strtolower($pie[1]));
        if ($month_n < 10) $month_n = '0' . $month_n;

        $year = (int)date('Y');
        if ((date('m') == 1) && ($month_n == 12)) $year--;
        if ((int)$pie[0] < 10) $pie[0] = '0' . $pie[0];

        return $pie[0] . '-' . $month_n . '-' . $year . ' ' . $pie[2];
    }

    private static function upper_month($str)// делать только если не обрезали первый 0 в дате месяца
    {
        $s = explode(' ', $str);
        $s[1] = mb_convert_case($s[1], MB_CASE_TITLE, "UTF-8");

        return $s[0] . ' ' . $s[1];
    }
}