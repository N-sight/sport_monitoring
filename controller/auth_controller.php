<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 31.08.2016
 * Time: 15:52
 */

class Authcontroller extends Controller
{
    function __call($name, $params)
    {
        e404(" В Authcontroller нет метода $name");
    }

    function  __construct ()
    {
        $this->layout = 'login_layout.php'; // в этой ветке контроллера Лейаут будет альтернативным и своим :)
    }

        function auth_login()
    {

        if (count($_POST)) {
            if ($_POST['__action'] === 'login') {
                $username = strip_tags($_POST['username']);
                $password = strip_tags($_POST['password']);


                // todo добавить информацию о галочке "Эапомнить меня"
                $user = new User();

                if ($user->auth($username,$password)) {
                    System::set_message('success','Вы успешно вошли в систему');
                    header("Location: /");
                    die();

                }
                else
                {
                    System::set_message('error','Ошибка : Неправильный логин или пароль');
                    header("Location: /auth/login");
                    die();
                }

            }
        }

        return $this->render('auth/login', [

        ]);

    }

    public function auth_logout ()
    {
        unset ($_SESSION['username']);
        unset ($_SESSION['password']);

        header('Location: /auth/login');
        die();
    }
}
