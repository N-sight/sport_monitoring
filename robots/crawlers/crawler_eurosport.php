<?php
class Eurosport_crawler
{
    public static function get_news($mon_day, $finish_day)
    {
        $start_day = $mon_day['old'];
        $stop_day = $finish_day['old'];

        $result = array(); // Массив целевых новостей
        $j = 0; //индекс result

        $flow = array(); // Весь массив новостей.

        //причесываем время
        $today = get_today();
        $yesterday = get_yesterday();
        $today = $today['old'];
        $yesterday = $yesterday['old'];

        if ($start_day == $today) {
            $checktime = substr(date('c'), 0, -6) . '.' . rand(100, 999);
            $day_flag = 'today';
        } elseif ($start_day == $yesterday) {
            $checktime = substr(date('c', time() - 86400), 0, -14) . '23:59:59.' . rand(100, 999);
            $day_flag = 'yesterday';
        } else {
            $day = $mon_day['day'];
            $month = $mon_day['month_n'];

            if ($mon_day < 10) $month = '0' . $month;

            if (($month == 12) && (date('m') == 01)) $year = ((int)date('Y')) - 1; else $year = (int)date('Y');

            $checktime = $year . '-' . $month . '-' . $day . 'T23:59:59.' . rand(100, 999);
            $day_flag = 'another';
        }

        $hour = (int)date('G'); // текущий час на сервере
        $download_date = mb_substr($checktime, mb_strpos($checktime, 'T') - 2, 2);// контрольная дата скачивания информации нужна для сравнения в конце цикла.

        //Первый блок по сбору данных
        $c_it = 0; // count iteration
        $c = 0; // trans count array-result

        //                 http://www.eurosport.ru/_ajax_/story_v8_5/storylist_latest_v8_5.zone?O2=1&langueid=15&dropletid=160&domainid=144&sportid=22&AdPageName=home-sport&RefreshInterval=900&mime=text%2fxml&DeviceType=desktop&datemax=2017-06-04T15:27:10.44
        do {
            $url[$c_it] = 'http://www.eurosport.ru/_ajax_/story_v8_5/storylist_latest_v8_5.zone?O2=1&langueid=15&dropletid=160&domainid=144&sportid=22&AdPageName=home-sport&RefreshInterval=900&mime=text%2fxml&DeviceType=desktop&datemax=' . $checktime;
            $data[$c_it] = file_get_contents($url[$c_it]);// получаем XML документ со страницами новостей.

            $data_first_date[$c_it] = mb_substr($data[$c_it], mb_strpos($data[$c_it], 'data-first-date=') + 17, 23);
            $data_last_date[$c_it] = mb_substr($data[$c_it], mb_strpos($data[$c_it], 'data-last-date=') + 16, 23);
            if (mb_substr($data_first_date[$c_it], -1, 1) == "\"") $data_first_date[$c_it] = mb_substr($data_first_date[$c_it], 0, -1) . '0';
            if (mb_substr($data_last_date[$c_it], -1, 1) == "\"") $data_last_date[$c_it] = mb_substr($data_last_date[$c_it], 0, -1) . '0';

            /*writeln($data_first_date[$c_it]);
            writeln($data_last_date[$c_it]);*/

            $first_date[$c_it] = mb_substr($data_first_date[$c_it], 8, 2);
            $last_date[$c_it] = mb_substr($data_last_date[$c_it], 8, 2);


            $xml[$c_it] = XMLtoArray($data[$c_it]);
            $news = $xml[$c_it]['XML']['DIV'];

            for ($i = 0; $i < count($news); $i++) {

                //со временем
                $flow[$i + $c]['date'] = $news[$i]['DIV'][$i]['DIV'][$i]['DIV'][$i * 3 + 2]['DIV'][$i * 2]['content'];

                if (contains($flow[$i + $c]['date'], "мин")) {
                    $reduce_min = (int)mb_substr($flow[$i + $c]['date'], 0, mb_strpos($flow[$i + $c]['date'], ' '));

                    $utime = ((int)date('U')) - 60 * $reduce_min;
                    $flow[$i + $c]['date'] = date('d-m-Y H:i', $utime);
                } elseif (contains($flow[$i + $c]['date'], " ч")) {
                    $reduce_hour = (int)mb_substr($flow[$i + $c]['date'], 0, mb_strpos($flow[$i + $c]['date'], ' '));
                    $utime = ((int)date('U')) - 3600 * $reduce_hour;
                    $flow[$i + $c]['date'] = date('d-m-Y H:i', $utime);
                } elseif (mb_strpos($flow[$i + $c]['date'], 'Вчера ') !== false) {
                    $pie = explode(" ", $flow[$i + $c]['date']);

                    $yest_day = date('d-m-Y', time() - 86400);
                    $flow[$i + $c]['date'] = $yest_day . ' ' . $pie[2];
                } else // 02/06 в 17:50
                {
                    $pie = explode(" ", $flow[$i + $c]['date']);
                    $year = (int)date('Y');

                    $date = explode("/", $pie[0]); //02/06

                    if ((date('m') == 01) && ($date[1] == 12)) {
                        $year = $year - 1;
                    }
                    $flow[$i + $c]['date'] = $date[0] . '-' . $date[1] . '-' . $year . ' ' . $pie[2];
                }

                //c header-ом
                $header = $news[$i]['DIV'][$i]['DIV'][$i]['DIV'][$i * 3]['A'];
                if (isset($header[$i * 2]['content'])) $flow[$i + $c]['header'] = $header[$i * 2]['content']; else $flow[$i + $c]['header'] = $header;

                // c комментами
                $flow[$i + $c]['comm'] = 0;

                // c урлом
                $string_url = $news[$i]['DIV'][$i]['DIV'][$i]['DIV'][($i * 3) + 1]['A'][($i * 2) + 1]['HREF'];
                if (mb_substr($string_url, 0, 4) !== "http") $flow[$i + $c]['url'] = 'http://www.eurosport.ru' . $string_url;
                else $flow[$i + $c]['url'] = $string_url;

                // c типом
                if (isset($news[$i]['DIV'][$i]['DIV'][$i]['DIV'][$i * 3 + 2]['DIV'][$i]['SPAN']['content']))
                    $flow[$i + $c]['type'] = $news[$i]['DIV'][$i]['DIV'][$i]['DIV'][$i * 3 + 2]['DIV'][$i]['SPAN']['content']; // подглючивает на датах отличных от вчера и сегодня
                else {
                    foreach ($news[$i]['DIV'][$i]['DIV'][$i]['DIV'][$i * 3 + 2]['DIV'][$i]['SPAN'] as $key => $value) {
                        $flow[$i + $c]['type'] = $value['content'];
                    }
                }
            }
            $c = count($flow);
            $checktime = $data_last_date[$c_it];
            $c_it++;

            shortpause();
            $date_last_pub = mb_substr($flow[$c - 1]['date'], 0, 2);//дата последней публикации

        } while (($date_last_pub == $download_date) && ($c_it < 6));

        //var_dump($flow);die;

        for ($i = 0; $i < count($flow); $i++) {
            if (
                (
                    ($flow[$i]['type'] == 'Чемпионат России') ||
                    ($flow[$i]['type'] == 'Стыковые матчи РФПЛ-ФНЛ') ||
                    ($flow[$i]['type'] == 'Кубок Конфедераций') ||
                    ($flow[$i]['type'] == 'Товарищеские матчи') ||
                    ($flow[$i]['type'] == 'Лига чемпионов УЕФА') ||
                    ($flow[$i]['type'] == 'Футбол') ||
                    ($flow[$i]['type'] == 'Чемпионат России. ФНЛ') ||
                    ($flow[$i]['type'] == 'Трансферы')
                ) &&
                (
                    mb_substr($flow[$i]['date'], 0, 2) == $mon_day['day']
                )
            ) {
                $result[$j] = $flow[$i];
                $j++;
            }
        }

        //var_dump($result);die;

        return $result;
    }
}
