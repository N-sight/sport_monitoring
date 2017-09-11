<?php

class Championat_parser
{
    public static function get_comments($line)
    {

        /*$line = array(
          'date' => '07-06-2017 18:57',
          'header' => '«Спартак» заключил с Зобниным новый 4-летний контракт на улучшенных условиях',
          'url' =>  'http://www.championat.com/football/news-2821034-zobnin-podpisal-novyj-kontrakt-so-spartakom.html',
          'comm' => 32,
          'data-id' => 'news_2821034'
        );*/

        $comm = array();
        $t_comm = array(); // таргетированные комментарии , РЕЗУЛЬТАТ!
        $n = 0;
        $jsurl = 'https://c.rambler.ru/api/app/5/getComments?comments_sorting=date_asc&xid=' . $line['data-id'];

        $data = file_get_contents($jsurl);
        $json = json_decode($data);
        $count = count($json);


        for ($i = 0; $i < count($json); $i++) {
            $comm[$n]['text'] = $json[$i]->html;
            if ( $comm[$n]['text'] == '') continue;// пустой комментарий скорее всего удален модератором.
            
            $comm[$n]['username'] = $json[$i]->displayName;
            $comm[$n]['time'] = $json[$i]->createdAt;
            $comm[$n]['time'] = self::patch_time($comm[$n]['time']);
            $comm[$n]['url'] = $line['url'];
            $comm[$n]['header'] = $line['header'] . ' | <b>' . $line['date'] . '</b>';
            $n++;
        }

        // блок выбора и отображения комментов по теме.
        //$t_comm = target_com($comm);

        //deb($t_comm);
        return $comm;
    }

    private static function patch_time($str)
    {

        $utime = strtotime($str);
        $time = date('d-m-Y H:i', $utime);

        return $time;
    }

}