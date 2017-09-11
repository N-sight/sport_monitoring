<?php
class Sovsport_parser
{

    public static function get_comments($line)
    {
        /*$url = array(
            'date' => '01-09-2017 15:33',
            'header' => '12 игроков «Спартака» начали подготовку к матчу с «Рубином»',
            'url' =>  'http://www.sovsport.ru/news/text-item/1001804',
            'comm' => 0,
            );*/

        $id = mb_substr($line['url'], mb_strpos($line['url'], 'item/') + 5);
        $comm = array();

        if (mb_strpos($id, 'adfox') != true) {
            if ($line['type'] == 'fotonews') $jsurl = 'http://www.sovsport.ru/js/comment3_' . $id . '_1.js'; // костыль, полагаем, что к фоткам не будет комментов больше 99!
            elseif (strpos($line['url'], 'blog') !== false) $jsurl = 'http://www.sovsport.ru/js/comment17_' . $id . '_1.js';// костыль, полагаем, что к фоткам не будет комментов больше 99!
            elseif ($line['type'] == 'videonews') $jsurl = 'http://www.sovsport.ru/js/comment3_' . $id . '_1.js';// костыль, полагаем, что к фоткам не будет комментов больше 99!
            else {
                $jsurl = 'http://www.sovsport.ru/js/comment1_' . $id . '_1.js';
            }

            $data = file_get_contents($jsurl);
            if (!$data) {
                if (php_sapi_name() == 'CLI') echo ('ошибка при получении контента с ' . $jsurl . ' ' . $line['url']);
            return false; // такой неоднозначный выход из функции с выводом на CLI
            }

            $rows = explode("\n", $data);

            //shortpause();

            $comm = array();
            $n = 0;
            $j = 1; //счетчик страниц комментариев

            for ($i = 0; $i < count($rows); $i++) {
                if (strpos($rows[$i], '\'\'+') !== false) {
                    $comm[$n]['username'] = mb_substr($rows[$i], 5, mb_strpos($rows[$i], '"+\'') - 5);
                    $comm[$n]['time'] = self::patch_time(mb_substr($rows[$i + 1], 3, mb_strpos($rows[$i + 1], '\', ') - 3), $line['date']);
                    $comm[$n]['header'] = $line['header'] . ' | <b>' . $line['date'] . '</b>';
                    $comm[$n]['url'] = $line['url'];
                    if (strpos($rows[$i + 1], '<div class=\"user_answer') !== false) {
                        $str = mb_substr($rows[$i + 1], mb_strpos($rows[$i + 1], '</div>') + 6);
                        $comm[$n]['text'] = mb_substr($str, 0, mb_strpos($str, '", \'\''));
                    } else {
                        $comm[$n]['text'] = mb_substr($rows[$i + 1], mb_strpos($rows[$i + 1], '\', "') + 4, mb_strpos($rows[$i + 1], '", \'\', ') - mb_strpos($rows[$i + 1], '\', "') - 4);
                    }
                    $n++;
                }
            }
            $c = count($comm);

            if (($c == 0) && ($line['type'] !== 'topnewsHTTP')) // если ничего не нашли - значит сваливаем сразу не трахаем мозг
            {
                return $comm;
            }

            while ($c > 97) //они хз как назначают это число
            {
                $j++;
                $c = 0;
                $jsurl = 'http://www.sovsport.ru/js/comment1_' . $id . '_' . $j . '.js';
                $data = file_get_contents($jsurl);
                $rows = explode("\n", $data);
                for ($i = 0; $i < count($rows); $i++) {
                    if (strpos($rows[$i], '\'\'+') !== false) {
                        $comm[$n]['username'] = mb_substr($rows[$i], 5, mb_strpos($rows[$i], '"+\'') - 5);
                        $comm[$n]['time'] = self::patch_time(mb_substr($rows[$i + 1], 3, mb_strpos($rows[$i + 1], '\', ') - 3), $line['date']);
                        $comm[$n]['header'] = $line['header'] . ' | ' . $line['comm'] . ' | <b>' . $line['date'] . '</b>';
                        $comm[$n]['url'] = $line['url'];
                        if (strpos($rows[$i + 1], '<div class=\"user_answer') !== false) {
                            $str = mb_substr($rows[$i + 1], mb_strpos($rows[$i + 1], '</div>') + 6);
                            $comm[$n]['text'] = mb_substr($str, 0, mb_strpos($str, '", \'\''));
                        } else {
                            $comm[$n]['text'] = mb_substr($rows[$i + 1], mb_strpos($rows[$i + 1], '\', "') + 4, mb_strpos($rows[$i + 1], '", \'\', ') - mb_strpos($rows[$i + 1], '\', "') - 4);
                        }

                        $n++;
                        $c++;
                    }
                }
            }
        } else {
        }
        return $comm;
    }

    private static function patch_time($str, $mon_day)
    {
        /*if (mb_substr($line['time'], 0, 4) == '2017') $time = mb_substr($line['time'], -6, -4) . ':' . mb_substr($line['time'], -4, -2);
        else $time = mb_substr($line['time'], -14, -9);*/

        $t1 = explode(",", $str);
        $day = mb_substr($t1[0], 0, mb_strpos($t1[0], ' '));// NB тут какой-то особенный пробел!!!
        $month = mb_substr($t1[0], mb_strpos($t1[0], ' ') + 1);

        $year = (int)date('Y');
        $year_of_article = (int)date('Y', strtotime($mon_day));

        if (($year_of_article + 1 == $year) && (name_month_to_num($month == 12))) {
            $year = $year_of_article;
        }

        $date = $day . '-' . name_month_to_num($month) . '-' . $year . ' ' . $t1[1];

        $utime = strtotime($date);
        $time = date('d-m-Y H:i', $utime);

        return $time;
    }

}