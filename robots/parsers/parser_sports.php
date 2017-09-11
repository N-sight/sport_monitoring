<?php

class Sports_parser
{
    public static function get_comments ($url)
    {
        /*$url = array(
            'date' => '01-09-2017 19:47',
            'header' => '«Зенит» заявил Дзюбу на групповой этап Лиги Европы',
            'url' =>  'https://www.sports.ru/football/1055273364.html',
            'comm' => 197,
            'ss' => '<a target="_blank" href="https://www.sports.ru/football/1055273364.html">«Зенит» заявил Дзюбу на групповой этап Лиги Европы</a>'
             );*/

        $uw_id = $url['url']; //весь урл целиком
        $key_name = strip_tags($url['ss']);
        $u_id = mb_substr($uw_id, -15, 10); // для любых категорий

        $fromid = '';
        $comm = array();
        $t_comm = array(); // таргетированные комментарии , РЕЗУЛЬТАТ!
        $n = 0;
        $jsurl = 'http://www.sports.ru/api/comment/get/message.json?order_type=new&message_id=' . $u_id . '&message_class=Sports%3A%3ANews&limit=30' . $fromid . '&new_time=1&style=newjs';
        $data = file_get_contents($jsurl);

        if (!$data) {
            //die("была ошибка" . $uw_id);
            return false;
        }


        $experiment = json_decode($data);
        $count = $experiment->data->total_count;
        $num_of_views = ceil($count / 30);

        for ($k = 0; $k < $num_of_views; $k++) {
            $jsurl = 'http://www.sports.ru/api/comment/get/message.json?order_type=new&message_id=' . $u_id . '&message_class=Sports%3A%3ANews&limit=30' . $fromid . '&new_time=1&style=newjs';
            $data = file_get_contents($jsurl);

            if (!$data) {
                die("была ошибка на" . $k . "строке ");
            }

            $experiment = json_decode($data); // массив с 30 комментами к этой статье

            //var_dump($experiment);

            for ($i = 0; $i < count($experiment->data->comments); $i++)
            {
                $comm[$n]['id'] = $experiment->data->comments[$i]->id;
                $comm[$n]['username'] = $experiment->data->comments[$i]->user->name;
                $comm[$n]['time'] = self::convert_fulltime($experiment->data->comments[$i]->create_time->full);
                $comm[$n]['text'] = $experiment->data->comments[$i]->text;
                $comm[$n]['url'] = $uw_id;
                $comm[$n]['header'] = $key_name . ' | <b>' . $url['date'] . '</b>';

                if ( $comm[$n]['username'] == '') { var_dump($experiment);die();}
                $n++;
            }

            if (isset ($experiment->data->comments[29]->id)) {
                $fromid = '&from_id=' . $experiment->data->comments[29]->id;
            } else {
                $fromid = '';
            }

            //shortpause(); todo зависает вся прога из-за этого....
        }

        //$t_comm = target_com($comm);
        //deb($comm);
        return $comm;
    }

    private static function convert_fulltime($str)
    {
        $date = strtotime($str);
        $result = date('d-m-Y H:i', $date);
        return $result;
    }
}