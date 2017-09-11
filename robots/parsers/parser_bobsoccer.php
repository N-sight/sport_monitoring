<?php

class Bobsoccer_parser
{
    public static function get_comments($line)
    {
        $num = 0; // счетчик страниц краулера
        $url = ''; // переменная в которой будет содержатся урл страницы краулера.
        $data = array(); // массив сбора всех страниц , что выдает http сервер.
        $rows = array(); // разбитый на строчки массив data
        $blocknews = array(); //блок новостей из $rows
        $allnews = array(); // массив всех новостей, что обнаружили на входной странице
        $k = 0; // cчетчик результата
        $n = 0; // счетчик новостей конечновыделенных новостей
        $result = array(); // массив куда попадут новости нужные новости за день.
        $allcomments = array();


        /*$line['url'] = 'http://bobsoccer.ru/user/871/blog/?item=394946';
        $line['header'] = 'Луческу: "Спартак" - самый популярный в России, ему помогли стать чемпионом"';
        $line['comm'] = '320';
        $line['time'] = '26 Мая 10:58';*/

        $url = $line['url'];
        $line['comm'] = (int)$line['comm'];

        micropause();

        // блок сбора всех новостей со страницы
        $data [$num] = file_get_contents($url);
        $rows = explode("\n", $data [$num]); // разбираем чехарду на строчки

        // тут выделяем  html код новостного блока без разбора нужно/не нужно
        $blocknews = cutblock($rows, 'class="js Comments comments', 'class="js Template comments-reply"');

        // на выходе из цикла получаем массив html кода внутри новостного блока $blocknews
        //var_dump($blocknews);die;

        //разбираем код блока новостей на индивидуальные комментариев.

        $allnews[0] = self::parse_comments($blocknews, $line);

        //var_dump($allnews);die;
        if (isset($blocknews[6])) {
            if (contains($blocknews[6], 'button'))// значит комменты еще есть
            {
                $n = 0;
                $count = count($allnews[0]);
                do {
                    $n++;
                    $ext[$n] = self::get_add_bob($n, $line['url']);
                    if (!$ext[$n]) {
                        writeln('stuck!');
                        $ext[$n] = self::get_add_bob($n, $line['url']);
                    }
                    $allnews[$n] = self::parse_comments($ext[$n]['html'], $line, false);
                    $count = $count + count($allnews[$n]);
                } while (($line['comm'] > $count) && ($n < 7) && ($ext[$n]['left'] > 0));
            }
        }
        for ($i = 0; $i < count($allnews); $i++) {
            for ($j = 0; $j < count($allnews[$i]); $j++) {
                $allcomments[$k] = $allnews[$i][$j];
                $k++;
            }
        }

        //$result = target_com($allcomments);
        //deb($allcomments);
        return $allcomments;
    }

    private static function parse_comments($arr, $line, $convert = true)
    {
        $k = 0; // счетчик комментов со страницы
        $result = array();
        for ($i = 0; $i < count($arr); $i++) {
            if (strpos($arr [$i], 'js Comments_Element comment-post') !== false) //
            {
                $result[$k]['url'] = $line['url'];
                $result[$k]['header'] = $line['header'] . ' | ' . $line['date'];

                $str = ($convert) ? iconv('KOI8-R', 'utf-8', $arr[$i + 19]) : $arr[$i + 19];
                //$str = iconv('KOI8-R', 'utf-8', $arr[$i + 19]);
                $result[$k]['text'] = strip_tags($str);

                $time = ($convert) ? iconv('KOI8-R', 'utf-8', $arr[$i + 8]) : $arr[$i + 8];

                $result[$k]['time'] = self::patch_time_parse_bob(trim(strip_tags($time)));
                $result[$k]['username'] = cut_leftspaces(strip_tags(iconv('KOI8-R', 'utf-8', $arr[$i + 6])));
                $k++;
            }

        }
        return $result;
    }

    private static function patch_time_parse_bob($str)
    {
        if (contains($str, 'Сегодня')) {
            $pie = explode(" ", $str);
            $d = get_today();
            return $d['date'] . ' ' . $pie[1];
        }

        if (contains($str, 'Вчера')) {
            $pie = explode(" ", $str);
            $d = get_yesterday();
            return $d['date'] . ' ' . $pie[1];
        }

        $pie = explode(" ", $str);
        $month_n = (int)name_month_to_num(mb_strtolower($pie[1]));
        if ($month_n < 10) $month_n = '0' . $month_n;

        $year = (int)date('Y');
        if ((date('m') == 1) && ($month_n == 12)) $year--;

        //if ((int)$pie[0] < 10) $pie[0] = '0' . $pie[0];
        //deb($pie[0] . '-' . $month_n . '-' . $year . ' ' . $pie[2]);

        return $pie[0] . '-' . $month_n . '-' . $year . ' ' . $pie[2];
    }

    private static function get_add_bob($n, $url)
    {
        $n++;
        //shortpause();
        $id_page = substr($url, strpos($url, '=') + 1);
        $mtime = explode(' ', microtime());
        $msec = (string)round($mtime[0], 4) * 10000;
        if (strlen($msec) !== 4) {
            switch (strlen($msec)) {
                case 3:
                    $msec .= '0';
                    break;
                case 2:
                    $msec .= '00';
                    break;
                case 1:
                    $msec .= '000';
                    break;
            }
        }
        $utime = $mtime[1] . $msec;

        $jsurl = 'http://bobsoccer.ru/ajax.php?c=classComment&m=Comments&part=' . $n . '&ccn=classBlog&ci=' . $id_page . '&arc=&JsHttpRequest=' . $utime . '-xml';
        $data = file_get_contents($jsurl);
        $load = json_decode($data);
        $result['left'] = (int)$load->js->QuantityLeft;

        $result['html'] = $load->text;
        if (strlen($result['html']) == 0) return false;
        $result['html'] = explode("\n", $result['html']);

        return $result;
    }
}