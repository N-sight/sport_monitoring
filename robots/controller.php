<?php

require_once ('helper.php');
require_once ('db.php');
spl_autoload_register('class_autoloader');

if (isset($_POST['date']))
{
    if ($_POST['date'] == 'yesterday')
    {
        $mon_day = get_yesterday();
    }
    elseif ($_POST['date'] == 'today')
    {
        $mon_day = get_today();
    }
    else
    {
        $str = strip_tags($_POST['date']);
        $user_yest = date_valid($str);
        if ($user_yest) $mon_day = $user_yest;
        else
        {
            header ("Location: http://".$_SERVER['HTTP_HOST']
                .dirname($_SERVER['PHP_SELF'])
                ."/views/".'init.php');
            die();
        }
    }
}

// если дата выдачи старше 5 дней редирект header
if (!date_check5day($mon_day))
{
    $url = "Location: http://".$_SERVER['HTTP_HOST']
        .dirname($_SERVER['PHP_SELF'])
        ."/views/".'init.php';
    header($url);
    die();
}

$pre_mon = get_pre_yesterday ($mon_day);

/* --- Ручной ввод на случай глюка в новом году --- */
//$mon_day = '31 декабря';
//$pre_mon = '30 декабря';

$quality = [  //минимальное количество комментариев для захода парсера -1 нет фильтрации по ключевикам
    'sports' => 15,
    'championat' => 15,
    'sovsport' => 0,
    'sportexpress' => 1,
    'eurosport' => -1,
    'soccer' => 5,
    'bobsoccer' => 10,
];

if (isset($_POST['source']) && ((isset($_POST['date'])) || isset($_POST['custom_date']) ) )
{
    $source = strip_tags($_POST['source']);
    Fetcher_model::drain($source,$mon_day,$pre_mon,$quality[$source]);

}
else
{
    header("Location: http://".$_SERVER['HTTP_HOST']
        .dirname($_SERVER['PHP_SELF'])
        ."/views/".'init.php');
    die();
}