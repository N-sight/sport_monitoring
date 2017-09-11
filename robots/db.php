<?php
// для работы с базой данных

define ('MYSQL_SERVER','194.87.98.152');
define ('MYSQL_USER','admin_monitoring');
define ('MYSQL_PASSWORD','qYNppNaQ3YCoBrlpvcUb');
define ('MYSQL_DB','admin_monitoring');

function db_connect ()
{
    $link = mysqli_connect(MYSQL_SERVER,MYSQL_USER,MYSQL_PASSWORD,MYSQL_DB)
    or die('Error: '.mysqli_error($link));

    if (!mysqli_set_charset($link,'utf8mb4'))
    {
        printf('Error: '.mysqli_error($link));
    }

    return $link;
}

// вспомогательные функции БД

function source_mark($link,$source,$date)
{
    $source = trim(strip_tags($source));
    $date = trim(strip_tags($date));
    $utime = date ('U');
    $ip = get_ip();

    if (($source==false) || ($date==false)) return false;

    $t = "INSERT INTO `fetchers_log` (source,time,date,ip ) VALUES ('%s','$utime','%s','$ip')";

    $query = sprintf ($t,mysqli_real_escape_string($link,$source),mysqli_real_escape_string($link,$date));
    $result = mysqli_query($link,$query);

    if (!$result) die(mysqli_error($link));

    return true;
}

function get_last_source_mark($link,$source)
{
    $source = trim(strip_tags($source));

    $query = sprintf("SELECT * FROM `fetchers_log` WHERE `source` = '%s' ORDER BY `id` DESC LIMIT 1",$source);
    $result = mysqli_query($link,$query);

    if (!$result) die(mysqli_error($link));
    $last = mysqli_fetch_assoc($result);

    return $last;
}

// прямые функции парсеров

function is_doublecom ($link,$source,$text,$sqtime,$username) // смотрим одинаковые комментарии в прошлом на протяжении 4 дней.
{
    $flag = false;
    $source = trim(strip_tags($source));
    $query = "SELECT * FROM ".$source." WHERE ( (text ='".$text."' ) AND (user = '".$username."') AND (time = '".$sqtime."') )";
    $result = mysqli_query($link, $query);
    if (!$result)
    {
        die (mysqli_error($link));
    }

    while ($row = mysqli_fetch_assoc($result)) 
    {
        $flag = true;
    }
    
    return $flag;
}