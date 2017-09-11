<?php

Class System
{
    protected static $user;

    public static function get_user ()
    {
        if (self::$user === NULL)
        {
            self::$user = new User();
            self::$user->auth_flow();

        }
        return static::$user;
    }

    public static function post ($field = NULL, $default = NULL)
    {
        if ( $field !== NULL )
        {
            if ( isset ($_POST[$field])) // если поле существует - то возвращаем поле
            {
                if ($_POST[$field] != '')
                {
                    $g = (int) $_POST[$field]; // возвращаем число,если это число
                    if ((string) $g === $_POST[$field])
                    {
                        return $g;
                    }
                    else
                    {
                        return $_POST[$field];
                    }
                }
                else // если пользователь не заполнил поле формы.
                {
                    return $default;
                }

            }
            else // если поле не существует , то возвращаем дефолт
            {
                return $default;
            }
        }
        else // если поле не было запрошено то нужно вернуть весть $_POST
        {
            $result = $_POST;
            unset ($result['__action']);
            //пройтись по типам
            foreach ($result as $k =>$v)
            {
                $g = (int) $v; // пытаемся вернуть число , если это число и NULL если поле не заполнено
                if ((string) $g === $v)
                {
                    $result[$k]=$g;
                }

                if ($v === '')
                {
                    $result[$k] = NULL;
                }
            }
            return $result;
        }

    }

    public static function files ($field = NULL, $default = NULL)
    {
        if ( $field !== NULL )
        {
            if ( isset ($_FILES[$field])) // если поле существует - то возвращаем поле
            {
                if ($_FILES[$field] != '')
                {
                    $g = (int) $_FILES[$field]; // возвращаем число,если это число
                    if ((string) $g === $_FILES[$field])
                    {
                        return $g;
                    }
                    else
                    {
                        return $_FILES[$field];
                    }
                }
                else // если пользователь не заполнил поле формы.
                {
                    return $default;
                }

            }
            else // если поле не существует , то возвращаем дефолт
            {
                return $default;
            }
        }
        else // если поле не было запрошено то нужно вернуть весть $_POST
        {
            $result = $_FILES;
            //пройтись по типам
            foreach ($result as $k =>$v)
            {
                $g = (int) $v; // пытаемся вернуть число , если это число и NULL если поле не заполнено
                if ((string) $g === $v)
                {
                    $result[$k]=$g;
                }

                if ($v === '')
                {
                    $result[$k] = NULL;
                }
            }
            return $result;
        }

    }

    public static function get ($field = NULL, $default = NULL)
    {
        if ( $field !== NULL )
        {
            if ( isset ($_GET[$field])) // если поле существует - то возвращаем поле
            {
                if ($_GET[$field] !== '')
                {
                    return $_GET[$field];
                }
                else // если пользователь не заполнил поле формы.
                {
                    return $default;
                }

            }
            else // если поле не существует , то возвращаем дефолт
            {
                return $default;
            }
        }
        else // если поле не было запрошено то нужно вернуть весть $_POST
        {
            return $_GET;
        }

    }

    // передача сообщений в сессиях , для отметки флагов выполнения операций.(см контроллеры)
    public static function set_message ($type,$message)
    {
        $_SESSION[$type] = $message;
    }

    public static function get_message ($type)
    {
        
        if (isset ($_SESSION[$type]) )
        {
            $value = $_SESSION[$type];
            unset ($_SESSION[$type]);
            return $value;
        }
        else
        {
            return NULL;
        }
    }
    
}
