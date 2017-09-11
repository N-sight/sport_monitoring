<?php

class Model
{
    const FIELD_NOT_EXIST = 'FIELD_NOT_EXIST';
    const ID_ACCESS_DENIED = 'ID_ACCESS_DENIED';
    const OBJECT_NOT_EXIST = 'OBJECT_NOT_EXIST';
    const UPDATE_FAILED = 'UPDATE FAILED';
    const ALREADY_EXIST = 'ALREADY_EXIST';
    const CREATE_FAILED = 'CREATE_FAILED';
    const DELETE_FAILED = 'CREATE_FAILED';

    public $tablename;

    protected static $errors = array(
        self::FIELD_NOT_EXIST,
        self::ID_ACCESS_DENIED,
        self::OBJECT_NOT_EXIST,
        self::UPDATE_FAILED,
        self::ALREADY_EXIST,
        self::CREATE_FAILED,
        self::DELETE_FAILED
    );

    protected $error;

    protected static $behaviours = array();
    protected static $db = NULL;

    protected static $fields = array(); // тут храним поля базы mySQL
    protected static $field_types = array(); // тут храним типы полей из базы mySQL

    protected $is_loaded_from_db;
    protected $is_changed; //тру когда объект соответствует базе данных

    protected $data = array(); //внутренние поля класса
    protected $relations = array(); //данные из связанных таблиц


    public function __construct($id = NULL)
    {
        if (static::$fields === array()) // если не знаем поля сущности - надо загрузить поля сущности.
        {
            static::init_fields();
        }

        if ($id !== NULL)
        {
            $id = (int)$id;
            if ($this->one($id)) {// загружаем объект из БД
                $this->is_loaded_from_db = true;
                $this->error = success;
            } else {
                $this->error = error; // выкладываем в объект ошибку о загрузке
            }
        } else 
        {
            $this->is_loaded_from_db = false; // сообщаем объекту что заданный объект - пустой.
        }

        $this->is_changed = false; // для конструктора мы только загружаем данные ничего не меняли.
    }

    public function __get($field)
    {
        if ($field == 'error') return $this->error;

        if (isset($this->data[$field]))
        {
            return $this->data[$field]; // для того, чтобы прокатывало $a->id_house
        }
        else
        {
            if (isset($this->relations[$field]))
            {
                return $this->relations[$field]; // для того, чтобы прокатывало $a->name_type,
            }
            else
            {
                if (in_array($field,array_keys(static::$behaviours))) // если запрашиваемое поле содержится в массиве ключей
                    // в статичном массиве $behaviours наследника
                    /*
                     * для Objects true будет тогда, когда $field = 'type' || 'city'
                     * */
                {
                    $key = static::$behaviours[$field]['key'];// для Objects = 'type_id' || 'city_id'

                    if (static::$behaviours[$field]['type'] == 'one') // для Objects[type && city] == true
                    {
                        $class_name = static::$behaviours[$field]['class']; // для Objects = 'Type' || 'City'
                        $value = new $class_name($this->$key);
                    }
                    else
                    {
                        $relation_key = static::$behaviours[$field]['relation_key'];
                        $class_name = static::$behaviours[$field]['class'];
                        $value = $class_name::all( [$relation_key => $this->$key] );
                    }

                    return $this->relations[$field] = $value; // и это загружается в $relаtions по требованию

                }
            }

        }
        if (!(in_array($field,static::get_fields())))
        {
            return self::FIELD_NOT_EXIST;
        }
        else
        {
            return NULL;
        }

    }

    public function __set($field, $value)
    {
        if ($field == 'error') return $this->error = $value;

        if ( ( !in_array($field, static::$fields) )  && (!in_array($field,array_keys(static::$behaviours))) )
        {// $field не содержится в наших ключевых массивах
            return self::FIELD_NOT_EXIST;
        }
        else
        {
           $pie = substr($field, 0, 2); // мои айдишники все точно не id $pie - приводит Primary key - к id;
           if ($pie === 'id')
           {
               return self::ID_ACCESS_DENIED;
           }
           else
           {
                if ( in_array($field, static::get_fields() ) ) // если поле есть в полях
                {

                    // блок защиты от SQL - инъекций
                    if ( static::$field_types[$field] === 'int' )
                    {
                        $value = (int) $value;
                    }
                    if ( static::$field_types[$field] === 'string' )
                    {
                        $value = mysqli_real_escape_string(self::get_db(),$value);
                    }
                    
                    $this->data[$field] = $value; // ставим его

                    if ($this->is_loaded_from_db) //если стоял флаг - загружено из БД -
                    {
                        $this->is_changed = true; // ставим флаг - изменено
                    }

                    return $this->data[$field]; // на всякий случай возвращем то что хотели установить.
                }
                else
                {
                    if (in_array($field,array_keys(static::$behaviours))) //записываем в relations если поле есть в behaviors
                    {
                        $this->relations[$field] = $value;
                    }
                    else
                    {
                        return self::FIELD_NOT_EXIST;
                    }

                }
            }
        }

    }
    
    public static function get_db() // Метод public потому, что надо чем-то закрывать соединение //todo  придумать чем закрывать соединение с базой.
    { // проверяем  установлена ли база через TableName и потом если нет- инициализируем ее
        if (self::$db === NULL) {
            $link = mysqli_connect(server_name, username, pass_word, db_name);

            if (mysqli_connect_errno()) {
                echo "Ошибка подключения к базе данных :" . mysqli_connect_error();
                exit;
            }

            if (!mysqli_set_charset($link, 'UTF8')) {
                echo "Ошибка при загрузке набора символов UTF-8 :" . mysqli_error($link);
                exit;
            }
            self::$db = $link;
        }
        return self::$db;
    }

    protected static function get_fields() // возвращает поля и типы , при необходимости загружает их 
    {
        if (static::$fields === array()) 
        {// если поля базы неопределены то надо их загрузить
            static::init_fields();
        }
        return static::$fields;
    }

    protected static function init_fields() // получаем названия и типы полей
    {

        $table = static ::tableName();

        $query = "DESCRIBE `" . $table . "`";
        $result = mysqli_query(self::get_db(), $query); 
        while ($row = mysqli_fetch_assoc($result))
        {
            static::$fields[] = $row ['Field']; //получаем названия полей 
            if (strpos($row['Type'], '(')) 
            {
                $pie = explode('(', $row['Type'], 2);
                $row['Type'] = $pie [0];
            }
            static::$field_types[$row ['Field']] = $row ['Type']; // получаем типы
        }
    }

    protected static function fields_query() //собирает перечисление названий полей в скобочках
    {
        $fields = static:: get_fields();
        $result = '';
        foreach ($fields as $f) {
            if ($result !== '') $result .= ', ';

            $result .= "`$f`";
        }
        return $result;
    }

    protected function values_query() // собирает перечисление значений в скобочках
    {
        $fields = static:: get_fields();
        $result = '';
        foreach ($fields as $f)
        {

            if ($result !== '') $result .= ', ';

            if ((isset ($this->data[$f])) && ($this->$f !== NULL))
            {
                $z = (string) $this->$f;
                $result .= "'$z'";
            }
            else
            {
                $result .= "NULL";
            }
        }
        return $result;
    }

    protected function update_query($updated_fields = array())
    {
        $fields = array();

        if ($updated_fields === array())
        {
            $fields = static:: get_fields();
        } else
        {
            foreach ($updated_fields as $uf)
            {
                if (in_array($uf, static::get_fields()))
                {
                    $fields[] = $uf;
                }
            }
        }
        $result = '';
        // разбираем $fields для сборки mySQL update
        foreach ($fields as $f) // пишем для ключевого слова SET
        {
            if ($result !== '') $result .= ', ';
            $z = (string) $this->$f;
            if ((isset ($this->data[$f])) && ($z !== NULL))
            {
                $result .= "`$f` = '$z'";
            } else {
                $result .= "`$f`  = NULL";
            }
        }
        return $result;
    }

    public static function className ()
    {
        return 'Model';
    }

    public static function tableName()
    {    // класс который будет наследоваться должен переопределить эту
        // функцию в зависимости от того к какой таблице относится этот класс.
        
        return NULL;
    }

    public function is_changed() // делает публичными данные об изменении
    {
        return $this->is_changed();
    }

    public function is_loaded_from_db() // делает публичными данные об изменении БД
    {
        return $this->is_loaded_from_db;
    }

    public function load($data = array())
    {
        foreach($data as $k => $v)
        {

            if (!in_array($k, static::get_fields())) // проверяем на поля
            {
                return self::FIELD_NOT_EXIST;
            }
            else
            {
                // элемент защиты от внешнего влияния SQL инъекций. данные очищаем ,приводим типы.
                if ( static::$field_types[$k] === 'int' )
                {
                    $v = (int) $v;
                }
                if ( static::$field_types[$k] === 'string' )
                {
                    $v = mysqli_real_escape_string(self::get_db(),$v);
                }
                $this->data[$k] = $v;
            }

        }
        $this->error = success;
        return true;
    }
    
    public function one($id,$source = NULL)
    {
        static ::get_fields();

        $query = "SELECT * FROM `" . static::tableName() . "` WHERE `" . static::$fields[0] . "` = '$id'";
        //мы не знаем как называется первое поле сущности
        $result = mysqli_query(self::get_db(), $query);
        if ($row = mysqli_fetch_assoc($result)) {
            $this->error = success;
            return $this->load($row);
        } else {
            return false;
        }
    }

    public function is_loaded()
    {
        $id = static::$fields[0];
        if (isset ($this->data["$id"]))
        {
            $key = $this->data["$id"];
        }
        else $key=NULL;
        if ($key) return $this->$key !== NULL;
        else return false;

    }

    public function edit()
    {
        $id = static::$fields[0];
        $key = $this->data["$id"];

        if ($this->$id === NULL) return self::OBJECT_NOT_EXIST;

        $query = "UPDATE `" . static::tableName() . "` SET " . $this->update_query() . " WHERE `$id` = '$key'";
        $result = mysqli_query(self::get_db(), $query);

        if ($result) {
            $this->is_changed = false;
            $this->error = success;
            return true;
        } else {
            $this->error = error;
            return self::UPDATE_FAILED;
        }

    }

    public function add()
    {
        $id = static::$fields[0];
        if ($this->$id !== NULL) return self::ALREADY_EXIST;


        $query = "INSERT INTO `" . static::tableName() . "` (" . static::fields_query() . ") VALUES (" . $this->values_query() . ")"; 
        $result = mysqli_query(self::get_db(), $query);
        if ($result)
        {
            $this->data["$id"]= mysqli_insert_id(self::get_db());
            $this->is_changed = false;
            $this->error = success;
            return true;
        } else {
            mysqli_error(static::get_db());
            $this->error = error;
            return self::CREATE_FAILED;
        }
    }
    
    public function delete()
    {
        $id = static::$fields[0];
        $key = $this->data["$id"];
        if ($key === NULL) return self::OBJECT_NOT_EXIST;


        $query = "DELETE FROM `". static::tableName()."` WHERE `$id` = '$key' LIMIT 1";
        $result = mysqli_query(self::get_db(), $query);
        if ($result)
        {
            $this->data["$id"] = NULL;
            $this->is_changed = false; // изменений в базе данных нет относительно действия нет. Проще говоря БД соответствует полям. Хотя это может быть неудобно.
            $this->is_loaded_from_db = false;
            $this->error = success;
            return true;
        }
        else return self::DELETE_FAILED;

    }

    protected static function where_condition($array)
    {
        $result = '';
        foreach ($array as $k => $value)
        {
            if ($value == 'FIELD_NOT_EXIST') return $result=1; // читер
            $result .= "(`{$k}` = '{$value}')";
        }
        return $result;
    }

    public static function all ($condition = '1')
    {
        if (is_array($condition))
        {
            $condition = self::where_condition($condition); // self
        }

        $query = "SELECT * FROM `".static::tableName()."` WHERE {$condition}";
        $result = mysqli_query(self::get_db(), $query);
        $all = array();
        while ($row = mysqli_fetch_assoc($result))
        {
            $class_name = static::className();
            $one = new $class_name();
            /* @var $one Object */
            $one->load($row);
            if ( $one->error === success)
            {
                $all[] = $one;
            }

        }
        //var_dump($one::$behaviours);
        return $all;
    }
}