<?php
class Homecontroller extends Controller
{
    public static function className () //найти нужный класс который обрабатывает ту или иную функцию
    {
        return 'Homecontroller';
    }

    function __call($name, $params)
    {
        e404("В Homecontroller нет такого метода: {$name}");
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

    }
    
    public function home_start ()
    {
        return $this->render("homes/start", array( ));
    }
}
?>

