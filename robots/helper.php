<?php

//время
function date_valid ($str) //2017-05-26 
{

    if ( !((contains($str,"/")) || (contains($str,"-"))) )
    {
        return false;
    }


    $s3 =  mb_substr($str,6);// 05-27
    $s2 =  mb_substr($str,3,2); // месяц
    $s1 =  mb_substr($str,0,2); // день

    $n3 = (int) $s3;
    $n2 = (int) $s2;
    $n1 = (int) $s1;

    if ( ($n1 < 1) || ($n2 <1 ) || ($n3 <1 ) || ($n1>31) || ($n2>12) || ($n3<2000) ) // не числа и не в диапазоне ? - на выход
    {
        return false;
    }

    //var_dump($n1);var_dump($n2);var_dump($n3); die;

    $month = num_month_to_name($n2);

    if ($n1<10)
    {
        $day = '0'.$n1;
    }
    else
        $day = $n1;

    $result = array(
        'date-ru'    => $day." ".$month." ".$n3,
        'date'       => $day."-".$s2."-".$n3,
        'old'     => $day." ".$month,
        'day'     => $n1,
        'month'   => $month,
        'month_n' => $n2,
        'year'    => $n3,
        'sql' => $n3.'-'.$s2.'-'.$day
    );

    return $result;
}

function date_check5day($date)
{
    switch ($date['month'])
    {
        case 'января':
            $month = 'January'; break;
        case 'февраля':
            $month = 'February'; break;
        case 'марта':
            $month = 'March'; break;
        case 'апреля':
            $month = 'April'; break;
        case 'мая':
            $month = 'May'; break;
        case 'июня':
            $month = 'June'; break;
        case 'июля':
            $month = 'July'; break;
        case 'августа':
            $month = 'August'; break;
        case 'сентября':
            $month = 'September'; break;
        case 'октября':
            $month = 'October'; break;
        case 'ноября':
            $month = 'November'; break;
        case 'декабря':
            $month = 'December'; break;
    }
    $str = $date['day'].' '.$month.' '.$date['year'];
    $unix_mon = strtotime($str);
    $unix_today =(int) date('U');

    $diff = $unix_today-$unix_mon;
    $days = floor($diff/86400);

    if (($days <0) || ($days>5))
    {
        return false;
    }
    else
    {
        return true;
    }
}

function get_today()
{
    $month_n = date ("m");
    $day =  date ("d");
    $year = (int) date ('Y');

    $month = num_month_to_name($month_n);
    $result = array(
        'date-ru'    => $day." ".$month." ".$year,
        'date'       => $day."-".$month_n."-".$year,
        'old'        => $day." ".$month,
        'day'        => (int) $day,
        'month'      => $month,
        'month_n'    => (int) $month_n,
        'year'       => $year,
        'sql'        => $year."-".$month_n."-".$day
        );
    return $result;
}

function get_yesterday()
{
    $yesterday = date('d-m-Y', time() - 86400);
    $day = mb_substr ($yesterday,0,2);
    $month_n = mb_substr ($yesterday,3,2);
    $year = mb_substr($yesterday,6);
    $month = num_month_to_name($month_n);
    $result = array(
        'date-ru'    => $day." ".$month." ".$year,
        'date'       => $day."-".$month_n."-".$year,
        'old'     => $day." ".$month,
        'day'     => (int) $day,
        'month'   => $month,
        'month_n' => (int) $month_n,
        'year'    => (int) $year,
        'sql'     => $year."-".$month_n."-".$day
    );
    return $result;
}

function get_pre_yesterday($inday)
{
    $pie = explode (' ',$inday['date-ru']);
    switch ($pie[1])
    {
        case 'января':
            $month = 'January'; break;
        case 'февраля':
            $month = 'February'; break;
        case 'марта':
            $month = 'March'; break;
        case 'апреля':
            $month = 'April'; break;
        case 'мая':
            $month = 'May'; break;
        case 'июня':
            $month = 'June'; break;
        case 'июля':
            $month = 'July'; break;
        case 'августа':
            $month = 'August'; break;
        case 'сентября':
            $month = 'September'; break;
        case 'октября':
            $month = 'October'; break;
        case 'ноября':
            $month = 'November'; break;
        case 'декабря':
            $month = 'December'; break;
    }

    $yesterday = $pie[0].' '.$month.' '.$pie[2];
    $date = strtotime($yesterday);
    $date = $date - 86400;
    $pre_yesterday = date('d-m-Y', $date);

    $day = mb_substr ($pre_yesterday,0,2);
    $month_n = mb_substr ($pre_yesterday,3,2);
    $year = (int) mb_substr($pre_yesterday,6);

    $month = num_month_to_name($month_n);
    $result = array(
        'date-ru'    => $day." ".$month." ".$year,
        'date'       => $day."-".$month_n."-".$year,
        'old'     => $day." ".$month,
        'day'     => (int) $day,
        'month'   => $month,
        'month_n' => (int) $month_n,
        'year'    => $year,
        'sql'     => $year."-".$month_n."-".$day
    );
    return $result;
}

//паузы
function shortpause ()
{
    $rand = rand(0,3)*1000000;
    @usleep ($rand);
}

function micropause ()
{
    $rand = rand(0,500000);
    @usleep ($rand);
}

function shortpause2 ()
{
    $rand = rand(0,20)*1000000;
    usleep ($rand);
}

//отображение
function writeln ($str)
{
    echo $str.'<br>';
    return true;
}

function get_bold($c)
{
    require_once ('target.php');
    $j = array(); //массив присутствия ключевых слов
    $k = -1; // счетчик присутствия ключевых слов подразумевается, что функция будет вызываться там, где уже точно есть одно слово.
    $gen = ''; // в эту строку будем забивать пробелы от ключевых слов на замену.
    $stop_flag = array(); // массив флажков остановок по каждому ключевому слову
    $n = 0; // суммарное от флажков остановок
    $target = gettarget();// получаем целевые сообщения;

    do
    {
        for ($i=0;$i<count($target);$i++)
        {
            // writeln('Проверяем на наличие '.$target[$i]);
            if (strpos($c, $target[$i]) !== false)
            {
                $k++;
                $j[$k][0] = mb_strpos($c, $target[$i]); //где менять
                $j[$k][1] = $i; // на что менять

                $gen = '<b>'.$target[$i].'</b>';

                $c = str_replace($target[$i], $gen,$c );

                $stop_flag[$i] = 1;
            }
            else
            {
                $stop_flag[$i] = 0; // ничего не нашли по ключевому слову
            }
        }

        for ($i=0;$i<count($stop_flag);$i++)
        {
            $n = $n + $stop_flag[$i]; // суммируем все достижения
        }

    } while($n==0);

    return $c;
}

// конвертеры функции

function num_month_to_name ($num)
{
    $month = '';
    $num = (int) $num;
    switch ($num)
    {
        case 1:
            $month = 'января'; break;
        case 2:
            $month = 'февраля'; break;
        case 3:
            $month = 'марта'; break;
        case 4:
            $month = 'апреля'; break;
        case 5:
            $month = 'мая'; break;
        case 6:
            $month = 'июня'; break;
        case 7:
            $month = 'июля'; break;
        case 8:
            $month = 'августа'; break;
        case 9:
            $month = 'сентября'; break;
        case 10:
            $month = 'октября'; break;
        case 11:
            $month = 'ноября'; break;
        case 12:
            $month = 'декабря'; break;
    }
    return $month;

}

function name_month_to_num ($str)//тут фича с 08 - август2
{
    switch ($str)
    {
        case 'января':
            $result = 1;
            break;
        case 'февраля':
            $result = 2;
            break;
        case 'марта':
            $result = 3;
            break;
        case 'апреля':
            $result = 4;
            break;
        case 'мая':
            $result = 5;
            break;
        case 'июня':
            $result = 6;
            break;
        case 'июля':
            $result = 7;
            break;
        case 'августа':
            $result = 8;
            break;
        case 'сентября':
            $result = 9;
            break;
        case 'октября':
            $result = 10;
            break;
        case 'ноября':
            $result = 11;
            break;
        case 'декабря':
            $result = 12;
            break;
    }
     
    if (!isset($result)) $result = 0;
    
    return $result;
}

function month_name_convert_shorten_toru ($str)
{
    $strmon = false;
    switch ($str)
    {
        case 'Jan':
            $strmon = 'января';
            break;
        case 'Feb':
            $strmon = 'февраля';
            break;
        case 'Mar':
            $strmon = 'марта';
            break;
        case 'Apr':
            $strmon = 'апреля';
            break;
        case 'May':
            $strmon = 'мая';
            break;
        case 'Jun':
            $strmon = 'июня';
            break;
        case 'Jul':
            $strmon = 'июля';
            break;
        case 'Aug':
            $strmon = 'августа';
            break;
        case 'Sep':
            $strmon = 'сентября';
            break;
        case 'Oct':
            $strmon = 'октября';
            break;
        case 'Nov':
            $strmon = 'ноября';
            break;
        case 'Dec':
            $strmon = 'декабря';
            break;
    }
    return $strmon;
}

function month_name_convert_shorten_tonum ($str)
{
    $strmon = false;
    switch ($str)
    {
        case 'Jan':
            $strmon = '01';
            break;
        case 'Feb':
            $strmon = '02';
            break;
        case 'Mar':
            $strmon = '03';
            break;
        case 'Apr':
            $strmon = '04';
            break;
        case 'May':
            $strmon = '05';
            break;
        case 'Jun':
            $strmon = '06';
            break;
        case 'Jul':
            $strmon = '07';
            break;
        case 'Aug':
            $strmon = '08';
            break;
        case 'Sep':
            $strmon = '09';
            break;
        case 'Oct':
            $strmon = '10';
            break;
        case 'Nov':
            $strmon = '11';
            break;
        case 'Dec':
            $strmon = '12';
            break;
    }
    return $strmon;
}

// функции работы со строками

function get_between($content,$start,$end){
    $r = explode($start, $content);
    if (isset($r[1])){
        $r = explode($end, $r[1]);
        return $r[0];
    }
    return '';
} // вырезает из строки часть между 2 частями

function XMLtoArray($XML)
{
    /**
     * Convert XML to an Array
     *
     * @param string  $XML
     * @return array
     */
    $xml_array = array();
    $xml_parser = xml_parser_create();
    xml_parse_into_struct($xml_parser, $XML, $vals);
    xml_parser_free($xml_parser);
    // wyznaczamy tablice z powtarzajacymi sie tagami na tym samym poziomie
    $_tmp='';
    foreach ($vals as $xml_elem) {
        $x_tag=$xml_elem['tag'];
        $x_level=$xml_elem['level'];
        $x_type=$xml_elem['type'];
        if ($x_level!=1 && $x_type == 'close') {
            if (isset($multi_key[$x_tag][$x_level]))
                $multi_key[$x_tag][$x_level]=1;
            else
                $multi_key[$x_tag][$x_level]=0;
        }
        if ($x_level!=1 && $x_type == 'complete') {
            if ($_tmp==$x_tag)
                $multi_key[$x_tag][$x_level]=1;
            $_tmp=$x_tag;
        }
    }
    // jedziemy po tablicy
    foreach ($vals as $xml_elem) {
        $x_tag=$xml_elem['tag'];
        $x_level=$xml_elem['level'];
        $x_type=$xml_elem['type'];
        if ($x_type == 'open')
            $level[$x_level] = $x_tag;
        $start_level = 1;
        $php_stmt = '$xml_array';
        if ($x_type=='close' && $x_level!=1)
            $multi_key[$x_tag][$x_level]++;
        while ($start_level < $x_level) {
            $php_stmt .= '[$level['.$start_level.']]';
            if (isset($multi_key[$level[$start_level]][$start_level]) && $multi_key[$level[$start_level]][$start_level])
                $php_stmt .= '['.($multi_key[$level[$start_level]][$start_level]-1).']';
            $start_level++;
        }
        $add='';
        if (isset($multi_key[$x_tag][$x_level]) && $multi_key[$x_tag][$x_level] && ($x_type=='open' || $x_type=='complete')) {
            if (!isset($multi_key2[$x_tag][$x_level]))
                $multi_key2[$x_tag][$x_level]=0;
            else
                $multi_key2[$x_tag][$x_level]++;
            $add='['.$multi_key2[$x_tag][$x_level].']';
        }
        if (isset($xml_elem['value']) && trim($xml_elem['value'])!='' && !array_key_exists('attributes', $xml_elem)) {
            if ($x_type == 'open')
                $php_stmt_main=$php_stmt.'[$x_type]'.$add.'[\'content\'] = $xml_elem[\'value\'];';
            else
                $php_stmt_main=$php_stmt.'[$x_tag]'.$add.' = $xml_elem[\'value\'];';
            eval($php_stmt_main);
        }
        if (array_key_exists('attributes', $xml_elem)) {
            if (isset($xml_elem['value'])) {
                $php_stmt_main=$php_stmt.'[$x_tag]'.$add.'[\'content\'] = $xml_elem[\'value\'];';
                eval($php_stmt_main);
            }
            foreach ($xml_elem['attributes'] as $key=>$value) {
                $php_stmt_att=$php_stmt.'[$x_tag]'.$add.'[$key] = $value;';
                eval($php_stmt_att);
            }
        }
    }
    return $xml_array;
} // разбивает XML на массив

function contains($content,$str, $ignorecase=true)// выражения с символом & неловит!!!
{
    if ($ignorecase){
        $str = mb_strtolower($str);
        $content = mb_strtolower($content);
    }
    if (mb_strpos($content,$str) !== false)
    {
        return true;
    }
    else
    {
        return false;
    }
} // проверяет наличие в строке

function cutblock($rows,$string_start,$string_end)
{
    /**
     * Cut array from array by keywords
     *
     * $string_start must be upper than $string_end
     *
     */
    $cut = array();
    $flag = 0; // marker flag

    for ($i=0; $i<count($rows);$i++)
    {
        if (mb_strpos($rows[$i],$string_start)) $flag = 1;
        if (mb_strpos($rows[$i],$string_end)) $flag = 0;

        if ($flag == 1) array_push($cut,$rows[$i]);
    }
    
    return $cut;
} // вырезает блок в верстке по высоте для краулера

function cut_leftspaces($string)
{
    if (isset($string{0}))
    {
        while ($string{0} == ' ')
        {
            $string = mb_substr($string, 1);
        }
    }
    return $string;
} //вырезает пустое место слева в строке

function get_HTML_parameter ($data,$parameter_str)// возвращает параметр из HTML кода
{
    /*
     * $data - строка где есть искомый параметр
     * $parameter_str - строковое название парметра
    */
    $n = 0; // счетчик result
    $i = 0; // ползунок по строке
    $result = ''; //строка выдачи результата
    $k = 0; // флажок считывания результата

    if (!contains($data,$parameter_str)) return false;

    $length = mb_strlen($parameter_str);
    $start_point = mb_strpos($data,$parameter_str);
    $i = $length+$start_point;

    do
    {
        if (mb_substr($data,$i,1) == chr(34))
        {
            $k++;
        }
        if ($k == 1)
        {
            $result{$n} = mb_substr($data,$i,1);
            $n++;
        }
        $i++;
    }
    while ($k<2);
    unset($result[0]);
    $result =  implode('',$result);

    return $result;
}

function mb_str_replace($symbol,$replace,$instring)
{
    $pos = mb_strpos($instring,$symbol);
    if ( $pos == false)
    {
        return false;
    }
    else
    {
        $len = mb_strlen($symbol);
        $str_in = mb_substr($instring,0,$pos);
        $str_out = mb_substr ($instring,$pos+$len);
        return $str_in.$replace.$str_out;
    }


}


// функции фильтрации

function target_com($comm) //для выборки целевых комментариев в парсере
{
    $t_comm = array();
    $n = 0; //счетчик целевых комментариев
    require_once ('target.php'); // вставляем тут объекты конечного мониторинга
    $target = gettarget();

    for ($i=0; $i<count($comm); $i++)
    {
        for ($k=0; $k<count($target); $k++)
        {
            if (mb_strpos($comm[$i]['text'], $target[$k] ) !== false)
            {
                $t_comm[$n] = $comm[$i];
                $n++;
            }
        }
    }
    return $t_comm;
} // выделяет таргетированные комментарии для блока парсера новости

function target_com2($comm) //для выборки целевых комментариев в КОНТРОЛЛЕРЕ
{
    $t_comm = array();
    $n = 0; //счетчик целевых комментариев
    require_once ('target.php'); // вставляем тут объекты конечного мониторинга
    $target = gettarget();

    for ($i=0; $i<count($comm); $i++)
    {
        for ($j=0;$j<count($comm[$i]);$j++)
        {
            for ($k=0; $k<count($target); $k++)
            {
                if (mb_strpos($comm[$i][$j]['text'], $target[$k] ) !== false)
                {
                    $t_comm[$n] = $comm[$i][$j];
                    
                    //тут функция вырезания <br> в поле 'text' для лучшего отображения

                    //далее замена <br> на пробел, идентифицируемая экселем, как лишняя строка
                    while ( strpos( $t_comm[$n]['text'],'<br />') !== false) {
                        $t_comm[$n]['text'] = str_replace('<br />',' ', $t_comm[$n]['text']);
                    }

                    while ( strpos( $t_comm[$n]['text'],'<br/>') !== false) {
                        $t_comm[$n]['text'] = str_replace('<br/>',' ', $t_comm[$n]['text']);
                    }

                    while ( strpos( $t_comm[$n]['text'],'<br>') !== false) {
                        $t_comm[$n]['text'] = str_replace('<br>',' ', $t_comm[$n]['text']);
                    }

                    while ( strpos( $t_comm[$n]['text'],'</br>') !== false) {
                        $t_comm[$n]['text'] = str_replace('</br>',' ', $t_comm[$n]['text']);
                    }
                    
                    // делаем целевые комментарии жирными
                    $t_comm[$n]['text'] = get_bold( $t_comm[$n]['text'] );

                    $n++;
                }
            }
        }
    }
    return $t_comm;
} // выделяет таргетированные комментарии для блока парсера новости

function target_news ($lines,$c)
{
    $result = array();
    $target = array ();

    if ($c == -1) return $lines;

    $target[0] = 'Федун';
    $target[1] = 'Спартак';
    $target[2] = 'Промес';
    $target[3] = 'Родионов';
    $target[4] = 'Каррера';
    $target[5] = 'Мутко';
    $target[6] = 'Гинер';
    $target[7] = 'Червиченко';
    $target[8] = 'Романцев';
    $target[9] = 'Луческу';
    $target[10] = 'Бердыев';
    $target[11] = 'Миллер';
    $target[12] = 'Аленичев';
    $target[13] = 'Дзюба';
    $target[14] = 'Зенит';

    $n = 0;

    for ( $i=0; $i<count ($lines); $i++ )
    {
        $d_flag = 0;
        for ($k=0; $k<count($target); $k++)
        {
            if ( (contains($lines[$i]['header'], $target[$k] )) && ($lines[$i]['comm'] > 0) )
            {
                $result[$n] = $lines[$i];
                $n++;
                $d_flag = 1;
                break;
            }

            if ( ($lines[$i]['comm']>$c) && ($d_flag == 0) )
            {
                $result[$n] = $lines[$i];
                $n++;
                break;
            }
        }

    }

    return $result;
} // для выборки целевых новостей.

//отладочная функция
function deb ($var)
{
    var_dump($var);
    die();
}

function get_ip()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP']))
    {
        $ip=$_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
    {
        $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    elseif (!empty($_SERVER['REMOTE_ADDR']))
    {
        $ip=$_SERVER['REMOTE_ADDR'];
    }
    else
    {
        $ip='SERVER BOT';
    }
    return $ip;
}

//автолоадер

function class_autoloader($classname)
{
    //предполагаем краулер
    if ( contains ($classname, 'crawler' ) ) //crawler_sports
    {
        $class_string = mb_substr($classname,0,mb_strpos($classname,'_'));
        $name = preg_replace('/([a-z])([A-Z])/', '$1_$2', $class_string);

        /*$place = getcwd();
        $place = mb_substr($place,0, mb_strpos( $place,'robots')+6);
        $file_name = $place."/crawlers/crawler_".mb_strtolower($name,'utf-8').'.php';*/

        $file_name = "./crawlers/crawler_".mb_strtolower($name,'utf-8').'.php';
        //deb(php_sapi_name());

        if (php_sapi_name() == 'cli' ) $file_name = '.'.$file_name;

        if (file_exists($file_name))
        {
            require_once $file_name;
        }
        else
        {
            e404(' Не могу найти краулер '.$classname);
        }
    }
    elseif ( contains ($classname, 'parser' ) )
    { // если это не краулер - то может быть это парсер?

        $class_string = mb_substr($classname,0,mb_strpos($classname,'_'));

        $name = preg_replace('/([a-z])([A-Z])/', '$1_$2', $class_string);

        $file_name = "./parsers/parser_".mb_strtolower($name,'utf-8').'.php';

        if (php_sapi_name() == 'cli' ) $file_name = '.'.$file_name;

        if ( file_exists($file_name) )
        {
            require_once $file_name;

        }
        else
        {
            e404(' Не могу найти парсер '.$classname);
        }
    }
    elseif ( contains ($classname, 'model' ) )
    {

        $class_string = mb_substr($classname,0,mb_strpos($classname,'_'));

        $name = preg_replace('/([a-z])([A-Z])/', '$1_$2', $class_string);

        $file_name = "./models/model_".mb_strtolower($name,'utf-8').'.php';

        if (php_sapi_name() == 'cli' ) $file_name = '.'.$file_name;

        if ( file_exists($file_name) )
        {
            require_once $file_name;
        }
        else
        {
            e404(' Не могу найти модель '.$classname);
        }
    }
    else
    {
        e404(' Не могу найти библиотеку, содержащую метод ' . $classname);
    }
}

function e404($error = NULL)
{
    header ("HTTP/1.1 404 Not Found");
    die('404 Ошибка, потому что : '.$error);
}

//renders

function render($data = array())
{
    //ob_start();
    $view_name = 'delivery';
    $lib = "views/".$view_name.".php";

    foreach ($data as $key => $value)
    {
        $$key = $value;
    }

    if (file_exists($lib))
    {
        require_once ($lib);
    }
    else
    {
        header ("HTTP/1.1 404 Not Found");
        die('404 - нет такой вьюшки :'.$lib);
    }
    //$content = ob_get_contents();
    //ob_end_clean();

    return true;
}

function crawl_render($data = array())
{
    $view_name = 'crawl_result';
    $lib = "views/".$view_name.".php";

    foreach ($data as $key => $value)
    {
        $$key = $value;
    }

    if (file_exists($lib))
    {
        require_once ($lib);
    }
    else
    {
        header ("HTTP/1.1 404 Not Found");
        die('404 - нет такой вьюшки :'.$lib);
    }

    return true;
}

function tech_render($data = array())
{
    $view_name = 'debug';
    $lib = "views/".$view_name.".php";

    foreach ($data as $key => $value)
    {
        $$key = $value;
    }

    if (file_exists($lib))
    {
        require_once ($lib);
    }
    else
    {
        header ("HTTP/1.1 404 Not Found");
        die('404 - нет такой вьюшки :'.$lib);
    }
    return true;
}
