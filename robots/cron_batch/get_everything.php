<?php
error_reporting(E_ALL);
date_default_timezone_set('Europe/Moscow');
set_time_limit(0);

$f = array();
$allnews = array();

$list = [
     'sports',
     'championat',
     'sovsport',
     'sportexpress',
     'eurosport',
     'soccer',
     'bobsoccer',
];


if (isset($_SERVER['argv'][1]))
{
    $squrl=$_SERVER['argv'][1];
}
else die('Ошибка: нет параметра для парсинга ');
if (!in_array($squrl,$list)) die ('Ошибка: источник '.$squrl.'не подключён! ');

require_once ('../helper.php');
require_once ('../db.php');
spl_autoload_register('class_autoloader');

$yest_day = get_yesterday();

//$yest_day = get_pre_yesterday($yest_day);
//$yest_day = get_pre_yesterday($yest_day);

$mon_day = get_pre_yesterday($yest_day);
$pre_mon = get_pre_yesterday($mon_day);

$name_crawler = ucfirst($squrl).'_crawler';
$name_parser = ucfirst($squrl).'_parser';

$allnews = $name_crawler::get_news ($mon_day,$pre_mon);

for ($i=0; $i<count($allnews); $i++)
{
    $f[$i] = $name_parser::get_comments ($allnews[$i]);
}

// вот тут надо слить данные в БД
$link = db_connect(); // вынести наверх нельзя - почему-то к этому моменту мрет SQL сервер
$handle = fopen(getcwd() .'/fetchers_log'.date('d-m-Y').'.txt' , 'a');
$flag = 1; // флаг успеха
$warning = 0; // флаг предупреждения
$error = 0; //флаг ошибки

for ($i=0;$i<count($f);$i++)
{
    for($j=0;$j<count($f[$i]);$j++)
    {
        $username = mysqli_real_escape_string($link,$f[$i][$j]['username']);
        $time = date('Y-m-d H:i', mysqli_real_escape_string($link,strtotime($f[$i][$j]['time'])));
        $text =  mysqli_real_escape_string($link,$f[$i][$j]['text']);
        $url =  mysqli_real_escape_string($link,strip_tags($f[$i][$j]['url']));
        $header = mysqli_real_escape_string($link,$f[$i][$j]['header']);;


        //тут блок проверки.

        if ($username == '')
        {
            $str =  date('d-m-Y H:i').' '.$squrl.' '.$url.' '.$header.' '.$text.' '.$time.' username not found by fetcher'."\r\n";
            $warning = 1;
            fwrite($handle, $str);
        }

        if ($time == '')
        {
            $str =  date('d-m-Y H:i').' '.$squrl.' '.$url.' '.$header.' '.$text.' '.$username.' time not found by fetcher'."\r\n";
            $warning = 1;
            fwrite($handle, $str);
        }

        if ($text == '')
        {
            $str =  date('d-m-Y H:i').' '.$squrl.' '.$url.' '.$header.' '.$time.' '.$username.' text not found by fetcher'."\r\n";
            $warning = 1;
            fwrite($handle, $str);
        }

        if ($header == '')
        {
            $str =  date('d-m-Y H:i').' '.$squrl.' '.$url.' '.$text.' '.$time.' '.$username.' header not found by fetcher'."\r\n";
            $warning = 1;
            fwrite($handle, $str);
        }

        if ($url == '')
        {
            $str =  date('d-m-Y H:i').' '.$squrl.' '.$header.' '.$text.' '.$time.' '.$username.' url not found by fetcher'."\r\n";
            $warning = 1;
            fwrite($handle, $str);
        }

        if ((!is_doublecom($link,$squrl,$text,$time,$username)) && ($text !='') ) // записываем в БД только если нет дубля и это не пробел
        {
            $query = "INSERT INTO `" . $squrl . "` (url,header,text,time,user) VALUES ('$url','$header','$text','$time','$username')";
            $result = mysqli_query($link, $query);
            if (!$result) {
                $error = (string)mysqli_error($link);
                $str = date('d-m-Y H:i') . ' ' . $squrl . ' ERROR: ' . $error . ' ' . "$query" . "\r\n";
                fwrite($handle, $str);
                $flag = 0;
                $error = 1;
            }

        }
        elseif ($text !='') // если есть дубль,  и это не пустое место - записываем запись о дубле
        {
            $str =  date('d-m-Y H:i').' '.$url.' '.$header.' '.$text.' '.$time.' '.$username.' DOUBLE FOUND by fetcher'."\r\n";
            fwrite($handle, $str);
        }

    }
}

if ($flag == 1) // Фатальных ошибок не найдено.
{
    source_mark($link,$squrl,$mon_day['sql']);// отмечаемся в логе фетчеров
    if ($warning == 0 ) $str = date('d-m-Y H:i').' '.$squrl.' success '."\r\n";
    else $str = date('d-m-Y H:i').' '.$squrl.' WARNING '."\r\n";
    fwrite($handle, $str);
}

if ($warning == 1)
{
    // отправляем на почту варнинг
}
if ($error == 1)
{
    $headers  = "Content-type: text/html; charset=UTF-8 \r\n";
    $headers .= "From: CRON Reporter <me@naughtysight.ru>\r\n";

    $message = '
<html>
    <head>
        <title>Birthday Reminders for August</title>
    </head>
    <body>
        <a href="http://naughtysight.ru/robots/cron_batch/fetchers_log'.date('d-m-Y').'.txt">Today`s log</a>
    </body>
</html>';

    mail("info@warmer.ru", "CRON Error at : ".$squrl, $message,$headers);
}

mysqli_close($link);
fclose($handle);
die();

