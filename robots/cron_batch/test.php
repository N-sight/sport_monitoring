<?php
date_default_timezone_set('Europe/Moscow');
$link = mysqli_connect('194.87.98.152','admin_monitoring','qYNppNaQ3YCoBrlpvcUb','admin_monitoring');
mysqli_set_charset($link,'utf8mb4');

$handle = fopen(getcwd() .'/fetchers_log.txt' , 'a');

$flag = 1;
$url = 'http://www.sports.ru/football/1054463227.html';
$header = "Видео: Сергей Шнуров: «Питер не спит, снова гулять! Сделал «Зенит» в Питере — пять!» | 07-08-2017 23:35";
$text = "О, у Шнура есть страничка на Спортсе, круто (хотя и не спортивно 🍺).";
$time = '08-08-2017 00:59';
$user = 'set_charset';


$query = "INSERT INTO `sports.ru` (url,header,text,time,user) VALUES ( '$url','$header','$text','$time','$user')";
$result = mysqli_query($link,$query);

if (!$result) {

    $error = (string) mysqli_error($link);
    $str = $surl.' '.date('d-m-Y H:i').' ERROR: '.$error.' '."$query"."\r\n";
    fwrite($handle, $str);
    $flag = 0;
};

mysqli_close($link);

$str = 'test '.date('d-m-Y H:i').' success'."\r\n";
if ($flag ==1) {
    fwrite($handle, $str);
}
fclose($handle);

?>
