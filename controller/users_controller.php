<?php


class Userscontroller extends Controller
{

    public static function className () //найти нужный класс который обрабатывает ту или иную функцию
    {
        return 'Userscontroller';
    }

    function __call($name, $params)
    {
        e404("В Usercontroller нет метода $name");
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



    public function users_add ()
    {
        if ((int) System::get_user()->role !== User::ROLE_ADMIN )
        {
            header("Location: /auth/login");
            die();
        }

        $outadd = new User();
        if (count($_POST))
        {
            if ($_POST['__action'] === 'add')
            {
                $username = (string) strip_tags ($_POST['name']); // здесь вкрутить очистку от вирусов
                $password = (string) strip_tags ($_POST['pass']);
                $role = (int) strip_tags($_POST['role']);

                $outadd->username = $username;
               
                $outadd->role = $role;
                $outadd->create_password($password);

                if ( $outadd->add()!== Model::CREATE_FAILED )
                {
                    if  ( !( ($outadd->error === void) || ($outadd->error === repeats) )  )
                    {
                        System::set_message('success',"Пользователь {$outadd->id} добавлен успешно");
                        header("Location: /users/article/{$outadd->id}");
                        die();
                    }

                }
                else
                {
                    System::set_message('error','Пользователя добавить не удалось');
                    header("Location: /users/list");
                    die();
                }
            }
        }

        $roles = User::$roles;
        return $this->render("users/add_users", array( 'outadd' => $outadd , 'roles' => $roles ));
    }

    public function users_list()
    {
        $users = User::all();
        return $this->render('users/index_users', array('users' => $users ) );
    }

    public function users_article ($id)
    {
        $out = new User ($id);

        if (!$out->is_loaded() )
        {
            System::set_message('error','Ошибка : Пользователя не удалось найти по его идентификатору');
            header("Location: /users/list");
            die();
        }

        return $this->render("users/article_users", array('out' => $out));
    }
    
    public function users_delete ($id)
    {
        $out_del = new User ($id);


        if (!$out_del->is_loaded() )
        {
            System::set_message('error','Ошибка : Пользователя не удалось найти по его идентификатору');
            header('Location: /users/list');
            die();
        }

        // здесь логика ошибок.
        if ($id == 2) $out_del->error = first; // первого админа удалить нельзя.
        //if ($system->user->id == $id) $out_del->error = repeats; //запись под которой зашли удалить нельзя
        if (System::get_user()->id == $id)
        {
            $out_del->error = repeats;
        } //запись под которой зашли удалить нельзя

        if ( ($out_del->error !== first) && ($out_del->error !== repeats))
        {
            $out_del->error = void;
        }

        if (count($_POST) > 0)
        {
            if ($_POST['__action'] === 'delete')
            {
                $out_del->delete();


                if ($out_del->error === success)
                {
                    System::set_message('success','Пользователя успешно удалили из системы');
                    header('Location: /users/list');
                    die();
                }
                else
                {
                    System::set_message('error','Ошибка , с удалением пользователя id='.$id.'  что-то пошло не так');
                    header('Location: /users/list');
                    die();
                }
            }
        }



        return $this->render("users/delete_users",array( 'out_del' => $out_del ) );
    }

    public function users_change ($id)
    {

        $out_edit = new User ($id);

        if (!$out_edit->is_loaded() )
        {
            System::set_message('error','Ошибка : Пользователя не удалось найти по его идентификатору');
            header("Location: /users/list");
            die();
        }

        if (count($_POST)) // обработчики событий
        {
            if ($_POST['__action'] === 'edit')
            {
                $name = (string) strip_tags($_POST['name']);
                if ( !($name =='') )
                {
                    $tmp = $out_edit->username;
                    $out_edit->username = $name;
                    $out_edit->edit();

                    if ($out_edit->error == success)
                    {
                        System::set_message('success',' Имя пользователя изменили успешно');
                        header('Location: /users/list');
                        die();
                    }
                    else
                    {
                        System::set_message('error','Ошибка : Имя пользователя изменить не удалось');
                        $out_edit->username = $tmp;
                    }
                }
                else
                {
                    System::set_message('error', 'Ошибка : Пустые данные вводить нельзя');
                }
            }
        }

        return $this->render ("users/change_users", array ( 'out_edit' => $out_edit ) );


    }

}
