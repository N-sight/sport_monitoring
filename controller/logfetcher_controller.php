<?php


class Logfetchercontroller extends Controller
{
    public static function className () //найти нужный класс который обрабатывает ту или иную функцию
    {
        return 'Logfetchercontroller';
    }

    function __call($name, $params)
    {
        e404("В Logfetchercontroller нет такого метода: {$name}");
    }

    function __construct()
    {
        if ((int) System::get_user()->role == User::ROLE_ADMIN )
        {
            $this->layout = 'layout_adm.php'; // в этой ветке контроллера Лейаут будет альтернативным и своим :)
        }
        elseif (System::get_user()->role == NULL ) // любой авторизированный пользователь
        {
            header("Location: /auth/login");
            die();
        }
        else
        {
            System::set_message('warning','Недостаточно прав для доступа к разделу'.self::className());
            header("Location: /search/list");
            die();
        }
    }


    public function logfetcher_list ($page=1)
    {
        //$out = Logfetcher::all($page);

        $max = Logfetcher::get_max_lines();
        $pages = ceil($max/Logfetcher::LINES_PER_PAGE);
        $out = Logfetcher::get_page($page); 
            
        return $this->render ("logfetchers/index",array( 'out' => $out, 'page'=>$page, 'pages'=>$pages) );
    }
}

