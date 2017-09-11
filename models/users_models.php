<?php

class User extends Model
{
    const ROLE_USER = 1;
    const ROLE_ADMIN = 10;

    public static $roles = [
        self::ROLE_USER => 'Пользователь',
        self::ROLE_ADMIN => 'Администратор'
    ];

    public static $behaviours = [ // для связи с другими сущностями - при добавлении других полей можно будет пользоваться связанными объектами
    // например зафигать сюда profile
    ];

    protected static $fields = array();
    protected static $field_types = array();
    
    public static function className() //найти нужный класс который обрабатывает ту или иную функцию
    {
        return 'User';
    }

    public static function tableName()
    {
        return 'users';
    }

    private function check_repeat_tab_users()  //проверяет есть ли такое название юзера в таблице юзеров
    {
        $query = "SELECT count(*) FROM `users` WHERE `username` = '$this->username'";
        $result = mysqli_query(static::get_db(), $query);

        while ($row = mysqli_fetch_all($result))
        {
            if ($row[0][0] == 0) {
                return false;
            } else {
                return true;
            }
        }
    }

    public function add() //добавляет новый тег объекта с названием тег
    {
        if ($this->check_repeat_tab_users())
        {
            System::set_message('error', 'Ошибка : Пользователь с таким логином уже есть в базе данных');
            return $this->error = repeats;
        }

        if ($this->username == '')
        {
            System::set_message('error', 'Ошибка : Пустые данные вводить нельзя');
            return $this->error = void;
        }
        return parent::add();
    }

    public function delete()
    {
        if (parent::delete())
        {
            return $this->error = success;
        }
        else return $this->error = error;
    }

    private static function generate_salt()
    {
        $length = 32;
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    protected static function generate_password($password,$salt)
    {
        return md5(md5($password).$salt);
    }

    public function create_password($password)
    {
        $this->salt = static::generate_salt();
        $this->password = static::generate_password($password,$this->salt);
    }

    public function check_password($password)
    {
        if ($this->salt === NULL) return false;

        $check_string = static::generate_password($password,$this->salt);

        if ($check_string === $this->password)
        {
            return true;
        }
        return false;
    }

    public function auth_flow()
    {
        if ((isset($_SESSION['username']))&&(isset($_SESSION['password'])))
        {
            $username = $_SESSION['username'];
            $password = $_SESSION['password'];

            $this->get_username($username); // загружаем поля пользователя по имени пользователя

            if ($this->password === $password)  // проверка напрямую ,потому что пароль в куках и сессиях хранится в зашифрованном виде
            {
                return true;
            }
            else
            {
                $this->id = NULL;
                $this->username = NULL;
                $this->password = NULL;
                $this->salt = NULL;
                return false;
            }

        }
        else
        {
            if ((isset($_COOKIE['username']))&&(isset($_COOKIE['password'])))
            {
                $username = $_COOKIE['username'];
                $password = $_COOKIE['password'];
                $this->get_username($username); // функция загрузки полей пользователя по имени пользователя. по типу one();

                if ($this->password === $password) // проверка напрямую ,потому что пароль хранится в зашифрованнном виде
                {
                    $_SESSION['username'] = $_COOKIE['username'];
                    $_SESSION['password'] = $_COOKIE['password'];
                    return true;
                }
                else
                {
                    $this->id = NULL;
                    $this->username = NULL;
                    $this->password = NULL;
                    $this->salt = NULL;
                    return false;
                }
            }
            else
            {
                return false;
            }
        }
    }

    public function auth($username,$password,$remember = false)
    {
        $this->get_username($username);
        if ($this->check_password($password))
        {
            $_SESSION['username'] = $this->username;
            $_SESSION['password'] = $this->password;
            if ($remember) // $remember= галочка запомнить меня.
            {
                setcookie("username",$this->username,60*60*24*7);
                setcookie("password",$this->password,60*60*24*7);
            }
            return true;
        }
        else
        {
            return false;
        }
    }

    public function get_username($username)
    {
        $username = mysqli_real_escape_string(self::get_db(),$username);
        $query = "SELECT * FROM `".static::tableName()."` WHERE `username` = '{$username}' LIMIT 1";
        $result = mysqli_query(self::get_db(),$query);

        if ($row = mysqli_fetch_assoc($result))
        {
            $this->load($row);
        }
    }


}