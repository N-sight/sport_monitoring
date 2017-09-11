<?php


class Searchusercontroller extends Controller
{

    function __call($name, $params)
    {
        e404("В Searchcontroller нет такого метода: {$name}");
    }

    function __construct()
    {
        if ((int) System::get_user()->role == User::ROLE_ADMIN )
        {
            $this->layout = 'layout_adm.php'; // в этой ветке контроллера Лейаут будет альтернативным и своим :)
        }

        if (System::get_user()->role == NULL ) // любой авторизированный пользователь
        {
            header("Location: /auth/login");
            die();
        }
    }


    public function searchuser_list ()
    {
        if (count($_POST))
        {
            $p =  strip_tags ($_POST['__action']);
            if ($p == 'add')
            {
                $request = strip_tags($_POST['username']);
                $source = strip_tags($_POST['source']);
                if (mb_strlen($request)<1)
                {
                    System::set_message('error', 'Ошибка : Слишком короткий поисковый запрос. Длина должна быть больше 3 знаков');
                    unset ($_POST);
                    header("Location: /searchuser/list");
                    die();
                }

                $start = date('Y-m-d H:i',strtotime(strip_tags($_POST['dateStart'])) );
                $end = date('Y-m-d',strtotime(strip_tags($_POST['dateEnd'])));
                $end = $end.' 23:59';

                Searchuser::$table =  $source;
                $out = Searchuser::get_request($request,$start,$end);

                $last_time = Searchuser::get_last_source_mark($source);

                return $this->render ("searchs/yandex",array ('out'=>$out,'request' => $request,'last_time'=>$last_time));
            }
            else
            {
                header("Location: /users/list");
                die();
            }
        }
        else
        {
            return $this->render ("searchusers/index");
        }
    }
}

