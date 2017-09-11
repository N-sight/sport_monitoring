<?php
//phpinfo();die;
date_default_timezone_set('Europe/Moscow');
error_reporting(E_ALL);
session_start();
require_once ('helpers/const.php'); // Константы тут
require_once ('helpers/array_helper.php');
require_once ('helpers/functions.php');
require_once ('helpers/pic_helper.php');
require_once ('system/controller.class.php');
require_once ('system/model.class.php');
require_once ('system/system.class.php');

spl_autoload_register('class_autoloader'); // вызывает функцию class_autoloader из доступных где идет подгрузка нужного файла

// обработка ЧПУ
if (isset($_GET['route']))
{
    $g = strip_tags($_GET['route']);
    $pie = explode('/',$g);

    if (isset($pie[0]) && $pie[0] !=='')
    {
        $controller = $pie[0];
    }
    else
    {
        $controller = 'humans';
    }

    if (isset($pie[1]) && $pie[1] !=='')
    {
        $controller_action = $pie[1];
    }
    else
    {
        $controller_action = 'list';
    }

    if (isset($pie[2]) && $pie[2] !=='')
    {
        $request_id = (int) $pie[2];
    }
    else
    {
        $request_id = NULL;
    }
}
else
{
    $controller = 'home';
    $controller_action = 'start';
    $request_id = NULL;
}

$controller_class_name = name2controller_class_name ($controller);
$controller_function_name = $controller."_".$controller_action;

$controller_object = new $controller_class_name();

if ( $request_id !== NULL)
{
    $result = $controller_object -> $controller_function_name($request_id);
}
else
{
    $result = $controller_object -> $controller_function_name();
}


if ( $result ) echo $result;
mysqli_close(Model::get_db());
die();







