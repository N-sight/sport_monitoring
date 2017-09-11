<?php

class Object extends Model
{
    public static $behaviours = [
        'type' => [   //type & city - поля предназначенные для вызова через вьюшку соответствующих направлений.
            'key' => 'type_id', // как называется поле по которому мы связываемся в той модели.
            'class' => 'Type',
            'type' => 'one'],
        'city' => [   //type & city - поля предназначенные для вызова через вьюшку соответствующих направлений.
            'key' => 'city_id',
            'class' => 'City',
            'type' => 'one'],
        'link_tag' => [   //по аналогии
            'key' => 'id_house',
            'class' => 'Objects_link_tag', /// здесь нужен класс который обудет лезть в
            'type' => 'many',
            'relation_key' => 'house_id'],
        'tag' => [
            'key' => 'ALL', // для генерации исключения в where_condition
            'class' => 'Tag',
            'type' => 'many',
            'relation_key' => 'id_tag'],// id_tag
        'link_pic' =>[   //по аналогии
            'key' => 'id_house',
            'class' => 'Objects_link_pic', /// здесь нужен класс который обудет лезть в
            'type' => 'many',
            'relation_key' => 'house_id'],
        'pic' => [
            'key' => 'ALL', // для генерации исключения в where_condition
            'class' => 'Picture',
            'type' => 'many',
            'relation_key' => 'id_pic']
    ];

    protected static $fields = array();
    protected static $field_types = array();

    public static function className () //найти нужный класс который обрабатывает ту или иную функцию
    {
        return 'Object';
    }

    public static function tableName ()
    {
        return 'objects';
    }

    public function get_pics() // получаем удобные картинки для вьюшки через драйвер БД
    {
        $load_pics = $this->link_pic;
        $allpics = $this->pic;
        $pics_at_obj = array();

        if (count($load_pics) == 0) return $pics_at_obj;

        foreach ($load_pics as $key => $value)
        {
            $k = (int) $value->pic_id;
            $p = (int) $value->id_link_pics;
            array_push($pics_at_obj, [ 'pic_id' => $k, 'id_link_pics' => $p]); // массив айдишек картинок в таблице pic , увязанных с obj_id
        }

        foreach ($allpics as $key => $value)
        {
            $i=0;
            do // крутим тут pics_at_obj
            {
                if ( $pics_at_obj[$i]['pic_id'] == $value->id_pic )
                {
                    $pics_at_obj[$i]['picture'] = $value->picture;
                }
                $i++;
            }while ($i<count($pics_at_obj));
        }

        return $pics_at_obj;
    }

    public function get_tags() // получаем удобные теги для вьюшки через драйвер БД
    {
        $load_tags = $this->link_tag; // грузим у объекта теги в наличии
        $alltags = $this->tag;        // грузим вообще все теги подряд
        $tags_at_obj = array();

        if (count($load_tags) == 0) return $tags_at_obj;

        foreach ($load_tags as $key => $value)
        {
            $k = (int) $value->tag_id;
            $p = (int) $value->id_link;
            array_push($tags_at_obj, [ 'tag_id' => $k, 'id_link' => $p]); // массив айдишек картинок в таблице pic , увязанных с obj_id

        }

        foreach ($alltags as $key => $value)
        {
            $i=0;
            do // крутим тут tags_at_obj
            {
                if ( $tags_at_obj[$i]['tag_id'] == $value->id_tags )
                {
                    $tags_at_obj[$i]['title_tags'] =  $value->title_tags;
                }
                $i++;
            }while ($i<count($tags_at_obj));
        }
        
        return $tags_at_obj;
    }

    private function check_repeat_object() // проверяет , есть ли уже такой адрес  в таблице объектов служебная для add()
    {
        $query = "SELECT count(*) FROM `objects` WHERE `address` = '$this->address'";

        if ($result = mysqli_query(static::get_db(), $query)) {
            while ($row = mysqli_fetch_all($result)) {
                if ($row[0][0] == 0) {
                    return false;
                } else {
                    return true;
                }
            }
        } else {
            echo 'Ошибка передачи данных в базу ' . mysqli_error(static::get_db());
            return ($this->error = error);
        }
        // до сюда никто не доживет
        return true;
    }

    public function add()
    {
        if (($this->name == '') || ($this->address == '') || ($this->price == ''))
        {
            System::set_message('error', 'Ошибка : Пустые данные вводить нельзя');
            return ($this->error = void);
        }
        if ($this->check_repeat_object() == true)
        {
            System::set_message('error', 'Ошибка : Объект с таким адресом уже есть в базе данных');
            return ($this->error = repeats);
        }

        parent::add();
    }

    public function edit()
    {
        if (($this->name == '') || ($this->address == '') || ($this->price == ''))
        {
            return ($this->error = void);
        }

        parent::edit();

    }

    private function del_alltag() // Нужна для удаления объекта. Удаление у объекта всех тегов без спроса.
    {
        $id = static::$fields[0];
        $key = $this->data[$id];

        $query = "SELECT * FROM `rent`.`objects_link_tags` WHERE `house_id` = '$key'";

        $list = array();
        if ($result = mysqli_query(static::get_db(), $query)) {
            while ($row = mysqli_fetch_assoc($result)) {
                $list[] = $row;
            }
            if (count($list) == 0) return ($this->error = success); // список записей с объектом id - пуст.
            else {
                foreach ($list as $key => $value) { // тут надо удалить запись о присвоении тегов объекту по id_house в табличке objects_link_tags
                    $num = $list[$key]['id_link'];
                    $q = "DELETE FROM `rent`.`objects_link_tags` WHERE `objects_link_tags`.`id_link` = '$num'";
                    $res = mysqli_query(static::get_db(), $q);
                    if (!$res) {
                        echo 'Ошибка передачи данных в базу ' . mysqli_error(static::get_db());
                        return ($this->error = error);
                    }
                }
                return ($this->error = success);
            }
        } else return ($this->error = error);

        // в таблице `tags` ничего не удаляем сам тег может быть использован в других объектах
    }

    private function del_allpic() // Нужна для удаления объекта. Удаление у объекта всех картинок без спроса.
    {
        $id = static::$fields[0];
        $key = $this->data["$id"];

        $query = "SELECT * FROM `rent`.`objects_link_pics` LEFT JOIN `rent`.`pics` ON `objects_link_pics`.`pic_id` = `pics`.`id_pic` WHERE `house_id` = '$key'";

        $list = array();
        if ($result = mysqli_query(static::get_db(), $query)) {
            while ($row = mysqli_fetch_assoc($result)) {
                $list[] = $row; // list - массив записей из objects_link_pics отфильтрованный по $obj_id
            }
            if (count($list) == 0) return ($this->error = success); // список записей с объектом id - пуст.
            else {
                foreach ($list as $key => $value) {

                    //здесь удаляется запись из таблицы objects_link_pics
                    $num = $list[$key]['id_link_pics'];
                    $q = "DELETE FROM `rent`.`objects_link_pics` WHERE `objects_link_pics`.`id_link_pics` = '$num'";
                    $res = mysqli_query(static::get_db(), $q);
                    if (!$res) {
                        echo 'Ошибка передачи данных в базу `objects_link_pics`' . mysqli_error(static::get_db());
                        return ($this->error = error);
                    }

                    // здесь необходимо удалить картинку физически на диске

                    if (!($list[$key]['picture'] === pic_default))  // удалять дефолтное изображение нельзя.
                    {
                        del_file($list[$key]['picture']);
                    }


                    //здесь удаляется запись из таблицы pics
                    $n2 = $list[$key]['pic_id'];
                    $q2 = "DELETE FROM `rent`.`pics` WHERE `id_pic` ='$n2'";
                    $r2 = mysqli_query(static::get_db(), $q2);
                    if (!$r2) {
                        echo 'Ошибка передачи данных в базу `pics`' . mysqli_error(static::get_db());
                        return ($this->error = error);
                    }

                }
                return ($this->error = success);
            }
        } else return ($this->error = error);
    }

    public function delete()
    {
        $this->error = null;
        // процедура проверки есть ли теги у объекта и удаление их.
        $this->del_alltag();
        

        // тут надо проверить есть ли у объекта картинки и удалить их из таблицы object_link_pics && pics && и на диске
        if ($this->error != error) $this->del_allpic();

        if ($this->error != error)
        {
            if (parent::delete())
            {
                $this->error = success;
            }
            else
            {
                $this->error = error;
            }
        }

    }
    
    //  todo плохо привязаны к драйверу БД !!!!
    public function get_tags_old() // Альтернативный метод доступа к тегам
    {

        $query = "SELECT * FROM `rent`.`objects_link_tags` LEFT JOIN `rent`.`tags` ON `objects_link_tags`.`tag_id` = `tags`.`id_tags` WHERE `house_id` = '$this->id_house'";


        $get = array();
        if ($result = mysqli_query(self::get_db(), $query)) {
            while ($row = mysqli_fetch_assoc($result))
            {
                $get[] = $row;
            }
        }
        else
        {
            echo 'Ошибка передачи данных в базу ' . mysqli_error(static::get_db());
            return ($this->error = error);
        }
        return $get;
    }

    public function check_tag_exist_3mix($id_tag) // возвращает тру , если находит в промежуточной таблице записи с (id_obj && id_tag)
    { //todo метод плохо приявязан к объекту
        $query = "SELECT * FROM `rent`.`objects_link_tags` LEFT JOIN `rent`.`tags` ON `objects_link_tags`.`tag_id` = `tags`.`id_tags` WHERE `house_id` = '$this->id_house'";
        if ($result = mysqli_query(static::get_db(), $query)) {
            while ($row = mysqli_fetch_assoc($result)) {
                $check[] = $row;
            }

            if (!(isset ($check))) return false;
            else {
                foreach ($check as $key => $value) {
                    if ($value['tag_id'] == $id_tag) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public function add_tag($id_tag) // добавляет тег к объекту.
    {
        if ($this->check_tag_exist_3mix($id_tag)) return $this->error = repeats;

        $query = "INSERT INTO `objects_link_tags` (`house_id`,`tag_id`) VALUES ('$this->id_house','$id_tag')";
        $result = mysqli_query(static::get_db(), $query);
        if (!$result) {
            echo 'Ошибка передачи данных в базу ' . mysqli_error(static::get_db());
            return $this->error = error;
        }
        return $this->error = success;
    }

    public function delete_tag($id_link) // удаляет запись связи id_obj и id_tag в сущности objects_link_tag
    {
        $query = "DELETE FROM `rent`.`objects_link_tags` WHERE `objects_link_tags`.`id_link` = '$id_link';";
        $result = mysqli_query(static::get_db(), $query);
        if (!$result) {
            echo 'Ошибка передачи данных в базу ' . mysqli_error(static::get_db());
            return $this->error = error;
        }
        return $this->error = success;

    }

    public function isPics_double($picname_arr) // возвращает false, если в массиве $picname нет аналогичнычных названий картинок для уже существующих записей с этим  $id_obj
    {// используется в контроллере объектов в методе edit() для добавления картинок к объекту

        foreach ($picname_arr as $key => $value) {
            $file_name[$key] = $this->id_house . "_" . $picname_arr[$key];
        }

        //$exist_pics = get_row_objects_pic_list($id_obj); // тут массив уже существующих картинок у id_obj
        $exist_pics = $this->get_pics();

        foreach ($file_name as $k => $v) {
            foreach ($exist_pics as $key => $value) {
                if ($exist_pics[$key]['picture'] == $file_name[$k]) {
                    return true; //есть одинаковые файлы!!!!! true == ОШИБКА!
                }
            }
        }

        return false; // одинаковых файлов нет - это нормальный ход.
    }

    public static function delete_pic_link($id_link)
    {
        $query = "DELETE FROM `rent`.`objects_link_pics` WHERE `objects_link_pics`.`id_link_pics` = '$id_link';";
        $result = mysqli_query(static::get_db(), $query);
        if (!$result) {
            echo 'Ошибка передачи данных в базу ' . mysqli_error(static::get_db());
            return false;
        }
        return true;

    }

    public static function delete_row_tab_pics($idpic) // удаляет строку картинки по id_pic в таблице pics
    {
        $query = "DELETE FROM `rent`.`pics` WHERE `pics`.`id_pic` = '$idpic';";
        $result = mysqli_query(static::get_db(), $query);
        if (!$result) {
            echo 'Ошибка передачи данных в базу ';
            echo mysqli_error(static::get_db());
            return false;
        }
        return true;
    }

}


// Старый класс для информации

/*
class Object extends Model
{
    private $id_house;
    public $type_id;
    public $city_id;
    public $name;
    public $address;
    public $price;
    public $description;

    public $relations = array();
    public $error;

    public static function className () //найти нужный класс который обрабатывает ту или иную функцию
    {
        return 'Object';
    }

    public static function tableName ()
    {
        return 'objects';
    }


    function __construct($id = NULL)
    {
        if ($id !== NULL) {
            $this->id_house = $id;
            $this->init();
        }
    }

    function __set($name, $value)
    {
        if (mb_substr($name, 0, 9, 'utf-8') === 'relation_') {
            $field = mb_substr($name, 9, NULL, 'utf-8');
            $this->relations[$field] = $value;
        }
    }

    function __get($name)
    {
        if ($name === 'id_house') return ($this->id_house);
        if (mb_substr($name, 0, 9, 'utf-8') === 'relation_') {
            $field = mb_substr($name, 9, NULL, 'utf-8');
            if (isset($this->relations[$field])) return $this->relations[$field];
        }
        return NULL;
    }

    public function is_loaded()
    {
        return ($this->id_house !== NULL);
    }


    function load($array = array())
    {
        foreach ($array as $k => $v) {
            $this->$k = $v;
        }
    }


    public function init()
    {
        global $link;

        if ($this->id_house === NULL) return false;

        $query = "SELECT 
`objects`.`id_house` AS `id_house`,
`objects`.`type_id` AS `type_id`,
`objects`.`city_id` AS `city_id`,
`objects`.`name` AS `name`,
`objects`.`address` AS `address`,
`objects`.`price` AS `price`,
`objects`.`description` AS `description`,
`types`.`id_type` AS `relation_id_type`,
`types`.`name_type` AS `relation_name_type`
 FROM `objects` LEFT JOIN `types` ON `objects`.`type_id` = `types`.`id_type` 
WHERE `objects`.`id_house` = '$this->id_house'";


        if ($result = mysqli_query($link, $query)) {
            if ($row = mysqli_fetch_assoc($result)) {
                $tmp = get_oneCity($row['city_id']);
                $row['relation_city'] = $tmp ['title_city'];

                $this->load($row);
                return ($this->error = success);
            } else {
                $this->id_house = NULL;
                return ($this->error = void);
            }

        } else {
            echo 'Ошибка передачи данных в базу ' . mysqli_error($link);
            return ($this->error = error);
        }
    }


    public static function all() //выводит общий массив с таблицей объектов.
    {
        global $link;
        $query = "SELECT 
`objects`.`id_house` AS `id_house`,
`objects`.`type_id` AS `type_id`,
`objects`.`city_id` AS `city_id`,
`objects`.`name` AS `name`,
`objects`.`address` AS `address`,
`objects`.`price` AS `price`,
`objects`.`description` AS `description`,
`types`.`id_type` AS `relation_id_type`,
`types`.`name_type` AS `relation_name_type`
 FROM `objects` LEFT JOIN `types` ON `objects`.`type_id` = `types`.`id_type` 
ORDER BY `id_house`";


        $get = array();
        if ($result = mysqli_query($link, $query)) {
            while ($row = mysqli_fetch_assoc($result)) {
                $tmp = get_oneCity($row['city_id']);
                $row['relation_city'] = $tmp ['title_city'];

                $object = new Object();
                $object->load($row);
                $get[] = $object;
            }

        }
        else
        {
            echo 'Ошибка передачи данных в базу ' . mysqli_error($link);
            return error;//($this->error = error);
        }
        return $get;
    }

    public function check_exist_object() //проверяет есть ли такой id в таблице объектов
    {
        global $link;
        $query = "SELECT count(*) FROM `objects` WHERE `id_house` = '$this->id_house'";

        if ($result = mysqli_query($link, $query)) {
            while ($row = mysqli_fetch_all($result)) {
                if ($row[0][0] == 0) {
                    return false;
                } else {
                    return true;
                }
            }
        } else {
            echo 'Ошибка передачи данных в базу ' . mysqli_error($link);
            return ($this->error = error);
        }

    }


    public function check_repeat_object() // проверяет , есть ли уже такой адрес  в таблице объектов
    {
        global $link;
        $query = "SELECT count(*) FROM `objects` WHERE `address` = '$this->address'";

        if ($result = mysqli_query($link, $query)) {
            while ($row = mysqli_fetch_all($result)) {
                if ($row[0][0] == 0) {
                    return false;
                } else {
                    return true;
                }
            }
        } else {
            echo 'Ошибка передачи данных в базу ' . mysqli_error($link);
            return ($this->error = error);
        }
    }

    public function add() // добавляет строку к таблице объектов
    {
        global $link;
        if (($this->name == '') || ($this->address == '') || ($this->price == '')) {

            return ($this->error = void);
        }
        if ($this->check_repeat_object() == true) {
            return ($this->error = repeats);
        }

        $query = "INSERT INTO `objects`( `type_id`,`city_id`, `name`, `address`, `price`, `description`) VALUES ('$this->type_id','$this->city_id','$this->name','$this->address','$this->price','$this->description')";
        $result = mysqli_query($link, $query);
        
        if ($result) {
            $id = mysqli_insert_id($link);
            $this->id_house = $id;
            return ($this->error = success);
        } else {
            echo 'Ошибка передачи данных в базу ' . mysqli_error($link);
            return ($this->error = error);
        }

    }

    public function del_alltag() // Нужна для удаления объекта. Удаление у объекта всех тегов без спроса.
    {
        global $link;
        $query = "SELECT * FROM `rent`.`objects_link_tags` WHERE `house_id` = '$this->id_house'";

        $list = array();
        if ($result = mysqli_query($link, $query)) {
            while ($row = mysqli_fetch_assoc($result)) {
                $list[] = $row;
            }
            if (count($list) == 0) return ($this->error = success); // список записей с объектом id - пуст.
            else {
                foreach ($list as $key => $value) { // тут надо удалить запись о присвоении тегов объекту по id_house в табличке objects_link_tags
                    $num = $list[$key]['id_link'];
                    $q = "DELETE FROM `rent`.`objects_link_tags` WHERE `objects_link_tags`.`id_link` = '$num'";
                    $res = mysqli_query($link, $q);
                    if (!$res) {
                        echo 'Ошибка передачи данных в базу ' . mysqli_error($link);
                        return ($this->error = error);
                    }
                }
                return ($this->error = success);
            }
        } else return ($this->error = error);

        // в таблице `tags` ничего не удаляем сам тег может быть использован в других объектах
    }

    public function del_allpic() // Нужна для удаления объекта. Удаление у объекта всех картинок без спроса.
    {
        global $link;
        $query = "SELECT * FROM `rent`.`objects_link_pics` LEFT JOIN `rent`.`pics` ON `objects_link_pics`.`pic_id` = `pics`.`id_pic` WHERE `house_id` = '$this->id_house'";

        $list = array();
        if ($result = mysqli_query($link, $query)) {
            while ($row = mysqli_fetch_assoc($result)) {
                $list[] = $row; // list - массив записей из objects_link_pics отфильтрованный по $obj_id
            }
            if (count($list) == 0) return ($this->error = success); // список записей с объектом id - пуст.
            else {
                foreach ($list as $key => $value) {

                    //здесь удаляется запись из таблицы objects_link_pics
                    $num = $list[$key]['id_link_pics'];
                    $q = "DELETE FROM `rent`.`objects_link_pics` WHERE `objects_link_pics`.`id_link_pics` = '$num'";
                    $res = mysqli_query($link, $q);
                    if (!$res) {
                        echo 'Ошибка передачи данных в базу `objects_link_pics`' . mysqli_error($link);
                        return ($this->error = error);
                    }

                    // здесь необходимо удалить картинку физически на диске

                    if (!($list[$key]['picture'] === pic_default))  // удалять дефолтное изображение нельзя.
                    {
                        del_file($list[$key]['picture']);
                    }


                    //здесь удаляется запись из таблицы pics
                    $n2 = $list[$key]['pic_id'];
                    $q2 = "DELETE FROM `rent`.`pics` WHERE `id_pic` ='$n2'";
                    $r2 = mysqli_query($link, $q2);
                    if (!$r2) {
                        echo 'Ошибка передачи данных в базу `pics`' . mysqli_error($link);
                        return ($this->error = error);
                    }

                }
                return ($this->error = success);
            }
        } else return ($this->error = error);
    }


    public function delete()
    {
        global $link;

        // процедура проверки есть ли теги у объекта и удаление их.
        $this->del_alltag();

        // тут надо проверить есть ли у объекта картинки и удалить их из таблицы object_link_pics && pics && и на диске
        $this->del_allpic();

        //переходим к удалению самой строки.
        $query = "DELETE FROM `rent`.`objects` WHERE `objects`.`id_house` = '$this->id_house'";
        $result = mysqli_query($link, $query);
        if (!$result) {
            echo 'Ошибка передачи данных в базу ' . mysqli_error($link);
            return ($this->error = error);
        }
        $this->id_house = NULL;
        return ($this->error = success);
    }


    public function edit() //обновляет строку объекта по id
    {
        global $link;
        $query = "UPDATE `objects` SET `type_id`= '$this->type_id',`city_id`='$this->city_id', `name`= '$this->name',`address`= '$this->address',`price`= '$this->price',`description`= '$this->description' WHERE `id_house` = '$this->id_house'";
        $result = mysqli_query($link, $query);
        if (!$result) {
            echo 'Ошибка передачи данных в базу ' . mysqli_error($link);
            return false;
        }
        return true;
    }

    public function check_tag_exist_3mix($id_tag) // возвращает тру , если находит в промежуточной таблице записи с (id_obj && id_tag)
    { //todo
        global $link;
        $query = "SELECT * FROM `rent`.`objects_link_tags` LEFT JOIN `rent`.`tags` ON `objects_link_tags`.`tag_id` = `tags`.`id_tags` WHERE `house_id` = '$this->id_house'";
        if ($result = mysqli_query($link, $query)) {
            while ($row = mysqli_fetch_assoc($result)) {
                $check[] = $row;
            }

            if (!(isset ($check))) return false;
            else {
                foreach ($check as $key => $value) {
                    if ($value['tag_id'] == $id_tag) {
                        return true;
                    }
                }
            }
        }
        return false;
    }


    public function objects_add_tag($id_tag) // добавляет тег к объекту. todo
    {
        global $link;

        if ($this->check_tag_exist_3mix($id_tag)) return repeats;

        $query = "INSERT INTO `objects_link_tags` (`house_id`,`tag_id`) VALUES ('$this->id_house','$id_tag')";
        $result = mysqli_query($link, $query);
        if (!$result) {
            echo 'Ошибка передачи данных в базу ' . mysqli_error($link);
            return false;
        }
        return true;
    }

    public function get_tags()
    {
        global $link;
        $query = "SELECT * FROM `rent`.`objects_link_tags` LEFT JOIN `rent`.`tags` ON `objects_link_tags`.`tag_id` = `tags`.`id_tags` WHERE `house_id` = '$this->id_house'";


        $get = array();
        if ($result = mysqli_query($link, $query)) {
            while ($row = mysqli_fetch_assoc($result))
            {
                $get[] = $row;
            }
        }
        else
        {
            echo 'Ошибка передачи данных в базу ' . mysqli_error($link);
            return ($this->error = error);
        }
        return $get;
    }

}*/
