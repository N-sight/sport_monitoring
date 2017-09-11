<?php
error_reporting(E_ALL);
set_time_limit(0);
date_default_timezone_set('Europe/Moscow');

$dir = 'http://'.$_SERVER['HTTP_HOST']
        .dirname($_SERVER['PHP_SELF'])
        .'/';
$version = '3.1';
setcookie('version',$version,time()+36000);


if ( !(isset($_POST['date'])) )
{
    header("Location: http://".$_SERVER['HTTP_HOST']
        .dirname($_SERVER['PHP_SELF'])
        ."/views/".'init.php');
    die();
}
else
{
    require_once ('controller.php');
}