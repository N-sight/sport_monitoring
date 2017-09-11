<?php
error_reporting(E_ALL);
include_once ('helper.php');
date_default_timezone_set('Europe/Moscow');

phpinfo();

$n=0;
do
{
    $n++;
    echo ($n);echo ('<br>');
}
while ( ($n%9 !== 0) || ($n<10));

$a = 'мама мыла раму';
echo mb_str_replace('мыла','стирала',$a);
