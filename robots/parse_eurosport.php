<?php

function page_eurosport($line)
{
    $k = 0; // счетчик result
    $allcomments = array ();
    $n = 0; // счетчик allcomments

    // проверка Lebuzz

    /*$line ['url']    = 'http://lebuzz.eurosport.ru/goals/10017/';
    $line ['date']   = '06-06-2017 12:55';
    $line ['comm']   = 0;
    $line ['header'] = 'Чудовищный привоз Де Брёйне и еще два гола из матча, в котором Бельгия разбила Чехию';*/

    // пытаемся получить Page_id
    if  (contains($line['url'],'lebuzz'))
    {
        $id_resource =  378570;
        $key_url = $line['url'].'stop';
        //http://data.livefyre.com/bs3/v3.1/eurosport-ru.fyre.co/378570/MTAwMTc=/init
        //http://lebuzz.eurosport.ru/goals/10017/
        http://data.livefyre.com/bs3/v3.1/eurosport-ru.fyre.co/378570/MTAwMjQ=/init
        $page_id = get_between($key_url,'goals/','/stop');
        if ($page_id == '') $page_id = get_between($key_url,'quiz/','/stop');
        if ($page_id == '') $page_id = get_between($key_url,'super/','/stop');
        if ($page_id == '') $page_id = get_between($key_url,'fun/','/stop');
        if ($page_id == '') $page_id = get_between($key_url,'stars/','/stop');
        if ($page_id == '') $page_id = get_between($key_url,'fails/','/stop');
        //http://lebuzz.eurosport.ru/quiz/10028/stop

    }
    else
    {
        $id_resource = 373117;
        $page = file_get_contents($line['url']);
        $data = explode("\n", $page);
        for ($i = 0; $i < count($data); $i++)
        {
            if (strpos($data[$i], 'data-typeNu') !== false) {
                $ref_id = get_HTML_parameter($data[$i], 'data-netsportId');
                $type_Nu = get_HTML_parameter($data[$i], 'data-typeNu');
                $page_id = $ref_id . '_' . $type_Nu;
                break;
            }
        }
    }

    //var_dump($page_id);

    if  ($line['url'] == 'http://www.eurosport.ru/football/fifa-confederations-cup/standingperson.shtml') return $allcomments; // тут нет комментариев

    if (!isset($page_id)) die('stuck in '.$line['url']);
    $page_code = base64_encode($page_id);
    shortpause();
    $jsurl = 'http://data.livefyre.com/bs3/v3.1/eurosport-ru.fyre.co/'.$id_resource.'/' . $page_code . '/init';
    $data = file_get_contents($jsurl);



    if (!$data)
    {
    die ('Ошибка при получении комментов с урла: '.$line['url']);
    }

    $json = json_decode($data);
    $count = count($json->headDocument->content);

    if ((!$count) && (!isset($json->headDocument)) ) die ('Ошибка при расшифровки с урла: '.$line['url'].' '.$jsurl);

    for ($i = 0; $i < $count; $i++) {
        if (isset($json->headDocument->content[$i]->content->bodyHtml)) {
            $allcomments[$n]['text'] = strip_tags($json->headDocument->content[$i]->content->bodyHtml);
            $date = $json->headDocument->content[$i]->content->createdAt;
            $allcomments[$n]['time'] = date ('d-m-Y H:i',$date);
            $allcomments[$n]['url'] = $line['url'];
            $allcomments[$n]['header'] = $line['header'];
            $allcomments[$n]['userid'] = $json->headDocument->content[$i]->content->authorId;

            $userid = (string) $allcomments[$n]['userid'];
            if (isset($json->headDocument->authors->$userid->displayName)) {
                $allcomments[$n]['username'] = $json->headDocument->authors->$userid->displayName;
            }
            else $allcomments[$n]['username'] = $userid;

            $n++;
        }
    }


    return $allcomments;

}
