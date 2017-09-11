<?php

class Soccer_parser
{
    public static function get_comments($line)
    {
        /*$line ['url']    = 'https://www.soccer0010.com/news/1009400.shtml';
        $line ['date']   = '02-09-2017 10:22';
        $line ['comm']   = 107;
        $line ['header'] = 'Ловчев: «Говорят, Гинер хочет продать ЦСКА, у него нет средств»';*/


        $result = array();
        $url = $line['url'];

        $maxnum = 1;
        $rowpage = explode("\n", file_get_contents($line['url']));


        for ($i = 0; $i < count($rowpage); $i++) {
            if (contains($rowpage[$i], 'pager-item last')) {
                $maxnum = (int)mb_substr($rowpage[$i], mb_strpos($rowpage[$i], 'мер') + 3, 3);// количество страниц с комментариями
                break;
            }
        }

        $num = 0; // счетчик страниц парсера
        $n = 0;
        $allnews = array(); // массив их блоков комментариев
        $blocknews = array(); //блок комментариев на одной странице

        do {
            micropause();
            $num++;
            if ($num == 1) {
                $url = $line['url'];
            } else {
                $url = $line['url'] . '?page=' . ($num - 1) . '#all-comments-list';
            }
            //writeln ('Заходим на страничку: '.$url);

            // блок сбора всех новостей со страницы
            $data [$num] = file_get_contents($url);
            $rows = array(); // разбитый на строчки массив страницы
            $rows = explode("\n", $data [$num]); // разбираем чехарду на строчки

            // тут выделяем  html код новостного блока без разбора нужно/не нужно
            $blocknews = cutblock($rows, 'Все комментарии</div>', '<div class="site-pager">');

            //var_dump($blocknews);die;

            for ($i = 0; $i < count($blocknews); $i++) {

                if (contains($blocknews[$i], 'div class="comment"')) {

                    for ($j = 6; $j < 9; $j++) {
                        if (contains($blocknews[$i + $j], '<div class="created">')) {
                            $allnews[$n]['time'] = self::patch_time_parse_soccer(trim(strip_tags($blocknews[$i + $j + 1])));  //Вчера 14:42
                        }
                    }

                    $j = 11;
                    do {
                        $j++;
                    } while ((!contains($blocknews[$i + $j], '<div class="commentBody')) && ($j < 70));

                    if ($j > 70) {
                        var_dump($i);
                        var_dump($i + $j);
                        var_dump($line['url']);
                        var_dump($blocknews);
                        die;
                    }

                    $k = 0;
                    $allnews[$n]['text'] = '';
                    do {
                        $k++;
                        $allnews[$n]['text'] = $allnews[$n]['text'] . trim(strip_tags($blocknews[$i + $j + $k]));
                    } while (!contains($blocknews[$i + $j + $k], '</div>'));

                    /*                for ($j=12;$j<25;$j++)
                                    {
                                        $k=1;$allnews[$n]['text'] = '';
                                        if (contains($blocknews[$i+$j],'<div class="commentBody">'))
                                        {
                                            do
                                            {
                                                $allnews[$n]['text'] = $allnews[$n]['text'].trim(strip_tags($blocknews[$i+$j+$k]));
                                                $k++;
                                            }
                                            while(!contains($blocknews[$i+$j+$k],'</div>') );
                                        }
                                    }*/


                    $allnews[$n]['url'] = $line['url'];
                    $allnews[$n]['header'] = $line['header'];

                    $allnews[$n]['username'] = cut_leftspaces(get_between($blocknews[$i + 6], '">', '</a>'));
                    $n++;
                }
            }

        } while ($num < $maxnum);

        //deb($allnews);

        return $allnews;
    }

    private static function patch_time_parse_soccer($str)
    {
        //
        if (contains($str, "сегодня")) {
            $pie = explode(" ", $str);
            $d = get_today();
            return $d['date'] . ' ' . $pie[2];
        }

        if (contains($str, 'вчера')) {
            $pie = explode(" ", $str);
            $d = get_yesterday();
            return $d['date'] . ' ' . $pie[2];
        }

        $pie = explode(' ', $str);
        $time = $pie[3];
        $day = $pie[0];
        $month = name_month_to_num($pie[1]);

        if ($month < 10) $month = '0' . $month;
        else $month = (string)$month;

        $today = get_today();
        $year = (int)$today['year'];

        if (($today['day'] == 1) && ($today['month_n'] == 1) && ($month == 12) && ($day > 26)) {
            $year = $year - 1;
        } elseif (($today['day'] == 2) && ($today['month_n'] == 1) && ($month == 12) && ($day > 27)) {
            $year = $year - 1;
        } elseif (($today['day'] == 3) && ($today['month_n'] == 1) && ($month == 12) && ($day > 28)) {
            $year = $year - 1;
        } elseif (($today['day'] == 4) && ($today['month_n'] == 1) && ($month == 12) && ($day > 29)) {
            $year = $year - 1;
        } elseif (($today['day'] == 5) && ($today['month_n'] == 1) && ($month == 12) && ($day > 30)) {
            $year = $year - 1;
        }

        $fulldate = $day . '-' . $month . '-' . $year . ' ' . $time;


        return $fulldate;

    }
}