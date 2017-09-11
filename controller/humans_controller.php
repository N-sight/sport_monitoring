<?php


class Humanscontroller extends Controller
{
    public static $sex = array(
        0 => 'Женщина',
        1 => 'Мужчина'
    );

    function __call($name, $params)
    {
        e404("В Humanscontroller нет такого метода: {$name}");
    }

    function __construct()
    {
        if (System::get_user()->role == NULL ) // любой авторизированный пользователь
        {
            header("Location: /auth/login");
            die();
        }
    }

    public function humans_add ()
    {
        $outadd = new Human ();
        $cities = City::all();
        $o =0;
        $pics = array();

        if (count($_FILES)) // физически работаем тут с загруженными файлами
        {
            $upload_pics = System::files()['picture'];

            if (($upload_pics['error'][0] != 4) && (isset($upload_pics['error'][0])) )
            {
                if (!image_upload(System::get_user()->username, $upload_pics, 'tmp')) // тут надо загрузить реально на диск
                {
                    var_dump($upload_pics);
                    clear_tmp_pic();
                    $upload_pics = NULL; // если вся пачка не загрузилась удаляем это барахло

                }
                else
                {
                    $pics = $upload_pics;
                }
            }

        }
        else
        {
            $upload_pics = array();
        }

        if (count($_POST))
        {
            $p =  strip_tags ($_POST['__action']);
            $object_data = System::post(); // прогружаем все данные в массив $object_data

            foreach ($object_data as $key => $value)
            {
                if( ($value === NULL) || ( (gettype($value) == 'int') && ($value === 0) ))
                {
                    System::set_message('error', 'Просьба заполнять все поля резюме, после этого можно сохранится или продолжить заполнять профессиональный опыт');
                    System::set_message('form',$object_data); // сюда перекидывем уже заполненные поля !!!
                    header("Location: /humans/add");
                    die();
                }
            }

            if ($p === 'add')
            {
                $outadd->load($object_data); // заносим их в объект
                if ( $outadd->add()!== Model::CREATE_FAILED)
                {
                    if (!(($outadd->error === void) || ($outadd->error === repeats))) {
                        if (isset($object_data['o'])) {
                            $o = $object_data['o']; //
                        } else {
                            $o = 0;
                        }
                        // обработка object_data - сюда приходят все, в том числе и возможно пустая, последняя запись

                        $exp = array(); // объявили пустой массив
                        for ($i = 0; $i < $o; $i++) {
                            $exp['org_name'] = $object_data['orgname'][$i];
                            $exp['region'] = (int)$object_data['city_id_work'][$i];
                            $exp['prof_id'] = (int)$object_data['prof_id'][$i];
                            $exp['hold_position'] = $object_data['hold_position'][$i];
                            $exp['start_date'] = $object_data['start_day'][$i];
                            $exp['end_date'] = $object_data['end_day'][$i];
                            $exp['just_now_flag'] = (int)$object_data['just_now_flag'][$i];
                            $exp['job_text'] = $object_data['job_text'][$i];

                            if (Exp::addexp($exp)) // прописываем запись о профессиональном опыте в таблицу `exp`
                            {
                                $id_exp = Exp::get_last_id_exp(); // получаем запись автоинкремента для таблицы `exp`
                                Link_human_exp::add_link($outadd->id_human, $id_exp);
                            } else {
                                System::set_message('error', "Ошибка при занесении профессионального опыта. Проверьте данные и сообщите в техподдержку.");
                                header("Location: /humans/article/{$outadd->id_human}");
                                die();
                            }

                        }

                        // если опыт записали успешно - загружаем картинки

                        $picture = array();

                        // физика загрузки картинки на диск

                        // скопировать схему из exp
                        // cделать проверку на пустой массив и присвоить пустышку , если ничего нет.
                        // занести всё в БД
                        // скопировать всё в /images/


                        $p_f = System::get_message('pics');
                        if ($p_f != NULL) {
                            // слить два массива
                            if (isset($pics['name'])) {
                                $max_pics = count($pics['name']);
                            } else $max_pics = 0;
                            $max_p_f = count($p_f['name']);
                            for ($i = $max_pics; $i < ($max_pics + $max_p_f); $i++) {
                                $pics['name'][$i] = $p_f['name'][$i - $max_pics];
                                $pics['type'][$i] = $p_f['type'][$i - $max_pics];
                                $pics['tmp_name'][$i] = $p_f['tmp_name'][$i - $max_pics];
                                $pics['error'][$i] = $p_f['error'][$i - $max_pics];
                                $pics['size'][$i] = $p_f['size'][$i - $max_pics];
                            }
                        }

                        if (isset($pics['name']))
                        {
                            $picture = form_pic_name($pics['name'], $outadd->id_human); // это должен быть массив названий  картинок
                            if ($pics['name'][0] == NULL)  // если картинки не загружены присваиваем  === pic_default
                            {
                                $picture [0] = pic_default;

                            }// массив для БД готов
                        }
                        else
                        {
                            $picture [0] = pic_default;
                        }
                        copy_tmp_pic($outadd->id_human);

                        foreach ($picture as $key => $value) // здесь записываем в базу данных
                        {
                            Pic::addpic($picture[$key],$outadd->id_human); //добавляем в таблицу pic - информацию о картинке
                        }

                        System::clear_session();
                        System::set_message('success', "Резюме {$outadd->id_human} добавлено успешно");
                        header("Location: /humans/article/{$outadd->id_human}");
                        die();
                    }
                }
                else
                {
                    System::set_message('error', 'Резюме добавить не удалось:');
                    header("Location: /humans/list");
                    die();
                }
            }

            if ($p === 'exp')
            {
                // момент итерации профессионального опыта
                if (isset($object_data['o']))
                {
                    $o = $object_data['o']+1; // по идее просто обновляется из hidden параметра O
                }
                System::set_message('form',$object_data); // сюда перекидывем уже заполненные поля !!!

                // работа с  картинками

                $p_f = System::get_message('pics');
                if ($p_f != NULL)
                {
                    // слить два массива
                    if (isset($pics['name'])) {
                        $max_pics = count($pics['name']);
                    }
                    else $max_pics = 0;

                    $max_p_f = count($p_f['name']);

                    for ($i=$max_pics ;$i<($max_pics+$max_p_f);$i++)
                    {
                        $pics['name'][$i] = $p_f['name'][$i-$max_pics];
                        $pics['type'][$i] = $p_f['type'][$i-$max_pics];
                        $pics['tmp_name'][$i] = $p_f['tmp_name'][$i-$max_pics];
                        $pics['error'][$i] = $p_f['error'][$i-$max_pics];
                        $pics['size'][$i] = $p_f['size'][$i-$max_pics];
                    }
                }
                System::set_message('pics',$pics);
                
                return $this->render("humans/add", array( "o"=>$o, 'cities' => $cities ));
            }

            if (mb_substr($p,0,3) === 'del')
            {
                $k = (int) mb_substr($p,3); //номер в массиве для удаления

                array_splice($object_data['orgname'],$k,1);
                array_splice($object_data['city_id_work'],$k,1);
                array_splice($object_data['hold_position'],$k,1);
                array_splice($object_data['start_day'],$k,1);
                array_splice($object_data['end_day'],$k,1);
                array_splice($object_data['job_text'],$k,1);
                System::set_message('form',$object_data);
                $o = $object_data['o'] -1;

                // работа с  картинками

                $p_f = System::get_message('pics');
                if ($p_f != NULL)
                {
                    // слить два массива
                    if (isset($pics['name']))
                    {
                        $max_pics = count($pics['name']);
                    }
                    else $max_pics = 0;

                    $max_p_f = count($p_f['name']);
                    for ($i=$max_pics ;$i<($max_pics+$max_p_f);$i++)
                    {
                        $pics['name'][$i] = $p_f['name'][$i-$max_pics];
                        $pics['type'][$i] = $p_f['type'][$i-$max_pics];
                        $pics['tmp_name'][$i] = $p_f['tmp_name'][$i-$max_pics];
                        $pics['error'][$i] = $p_f['error'][$i-$max_pics];
                        $pics['size'][$i] = $p_f['size'][$i-$max_pics];
                    }
                }
                System::set_message('pics',$pics);

                return $this->render("humans/add", array( "o"=>$o, 'cities' => $cities));
            }

        }
        return $this->render("humans/add", array('outadd' => $outadd, 'cities' => $cities, "o"=>$o ));
    }

    public function humans_article($id)
    {
        $out = new Human ($id);
        if (!$out->is_loaded()) {
            System::set_message('error', 'Ошибка : Резюме не удалось найти по его идентификатору');
            header("Location: /humans/list");
            die();
        }

        $cities = City::all();
        $exp_at_human = $out->get_exp();
        $pics_at_human = Pic::get_pics($id);

        return $this->render("humans/article", array(
                'out' => $out,
                'sex' => self::$sex,
                'cities' => $cities,
                'exp_at_human' => $exp_at_human,
                'pics_at_human' => $pics_at_human
            )
        );
    }

    public function humans_change ($id)
    {
        if ((int) System::get_user()->role !== User::ROLE_ADMIN )
        {
            header("Location: /auth/login");
            die();
        }

        $out_edit = new Human ($id);

        if (!$out_edit->is_loaded() )
        {
            System::set_message('error','Ошибка : Резюме не удалось найти по его идентификатору');
            header("Location: /humans/list");
            die();
        }

        $cities = City::all();
        $exp_at_human = $out_edit->get_exp();
        $pics_at_human = Pic::get_pics($id);

        $object_data = System::post();
        $o =0;

        if (count($_POST)) // обработчики событий
        {
            $p = strip_tags ($_POST['__action']);
            //основные поля
            if ($p === 'edit')  // Основные поля
            {
                $object_data = System::post();

                // обновили данные через result
                $out_edit->load($object_data);

                $out_edit->edit();

                if (!($out_edit->error === void))
                {
                    System::set_message('success', "Резюме {$id} изменили успешно");
                    header("Location: /humans/change/".$id);
                    die();
                }
                else
                {
                    System::set_message('error', "Ошибка : Скорее всего вы ввели пустые данные");
                    header("Location: /humans/change/".$id);
                    die();
                }

            }

            //работа с опытом
            if ($p == 'exp_edit')
            {
                $object_data = System::post();
                $failure_flag  = 0; // ошибок в этой процедуре не найдено
                $exp = array(); // объявили пустой массив
                $e = count($exp_at_human);
                $o = count($object_data['orgname']);

                for ($i=0;$i<$e;$i++)
                {
                    $exp['id_exp'] = $exp_at_human[$i]['exp_id'];
                    $exp['org_name'] = $object_data['orgname'][$i];
                    $exp['region'] = (int)$object_data['city_id_work'][$i];
                    $exp['prof_id'] = (int)$object_data['prof_id'][$i];
                    $exp['hold_position'] = $object_data['hold_position'][$i];
                    $exp['start_date'] = $object_data['start_day'][$i];
                    $exp['end_date'] = $object_data['end_day'][$i];
                    $exp['just_now_flag'] = (int)$object_data['just_now_flag'][$i];
                    $exp['job_text'] = $object_data['job_text'][$i];


                    $update_exp = new Exp($exp['id_exp']);
                    if (!$update_exp->is_loaded()) {
                        System::set_message('error', 'Ошибка : Запись профессонального опыта не удалось найти по его идентификатору. Обвал на итерации ' . $i);
                        header("Location: /humans/change/" . $id);
                        die();
                    }

                    $update_exp->load($exp);
                    $update_exp->edit();
                    if ($update_exp->error !== success) $failure_flag = 1;
                }

                // тут работаем с данными которые добавились к уже имеющимся

                $exp2 = array(); // объявили пустой массив
                for ($i=$e;$i<$o;$i++) // бежим по свободной части массива форм
                {
                    $exp2['org_name']  =  $object_data['orgname'][$i];
                    $exp2['region']  = (int) $object_data['city_id_work'][$i];
                    $exp2['prof_id']  = (int) $object_data['prof_id'][$i];
                    $exp2['hold_position']  =  $object_data['hold_position'][$i];
                    $exp2['start_date']  =  $object_data['start_day'][$i];
                    $exp2['end_date']  =  $object_data['end_day'][$i];
                    $exp2['just_now_flag']  = (int) $object_data['just_now_flag'][$i];
                    $exp2['job_text']  =  $object_data['job_text'][$i];

                    if(Exp::addexp($exp2)) // прописываем запись о профессиональном опыте в таблицу `exp`
                    {
                        $id_exp = Exp::get_last_id_exp(); // получаем запись автоинкремента для таблицы `exp`
                        Link_human_exp::add_link($out_edit->id_human, $id_exp);
                    }
                    else
                    {
                        $failure_flag = 1;
                    }
                }



                if ($failure_flag)
                {
                    System::set_message('error', "Ошибка при обновлении профессионального опыта. Проверьте данные и сообщите в техподдержку.");
                    header("Location: /humans/change/".$id);
                    die();
                }
                else
                {
                    System::set_message('success', "Профессиональный опыт в резюме {$id} изменили успешно");
                    header("Location: /humans/change/".$id);
                    die();
                }

            }

            if (mb_substr($p,0,3) === 'del')
            {
                $k = (int) mb_substr($p,3); //номер в массиве для удаления
                $id_link = $exp_at_human [$k]['id_link_human_exp'];
                $id_exp = $exp_at_human [$k]['exp_id'];


                array_splice($object_data['orgname'],$k,1);
                array_splice($object_data['city_id_work'],$k,1);
                array_splice($object_data['hold_position'],$k,1);
                array_splice($object_data['start_day'],$k,1);
                array_splice($object_data['end_day'],$k,1);
                array_splice($object_data['job_text'],$k,1);
                System::set_message('form',$object_data);
                $o = $object_data['o'];
                array_splice($exp_at_human,$k,1);

                $del_link = new Link_human_exp($id_link);
                if (!$del_link->is_loaded() )
                {
                    System::set_message('error','Ошибка : Запись СВЯЗИ профессонального опыта не удалось найти по его идентификатору.');
                    header("Location: /humans/change/".$id);
                    die();
                }
                $del_exp = new Exp($id_exp);
                if (!$del_exp->is_loaded() )
                {
                    System::set_message('error','Ошибка : Запись профессонального опыта не удалось найти по его идентификатору.');
                    header("Location: /humans/change/".$id);
                    die();
                }

                $del_link->delete();
                $del_exp->delete();

                if ( ($del_link->error === success ) && ($del_exp->error === success) )
                {
                    System::set_message('success', "Профессиональный опыт в резюме {$id} изменили успешно");
                    return $this->render ("humans/change", array(
                        'out_edit' => $out_edit , //передаем данные строки из таблицы objects по id
                        'cities'=> $cities,
                        'sex' => self::$sex,
                        'exp_at_human' => $exp_at_human, // профессиональный опыт.
                        'o' =>$o,
                        'pics_at_human' => $pics_at_human
                    ) );
                }
                else
                {
                    System::set_message('error','Ошибка : При удалении профессонального опыта что-то пошло не так.');
                    header("Location: /humans/change/".$id);
                    die();
                }
            }

            if ($p == 'insert_new')
            {
                if (isset($object_data['o']))
                {
                    $o = $object_data['o']+1; // по идее просто обновляется из hidden параметра O
                }
                else
                {
                    $o = 1;
                }
                System::set_message('form',$object_data); // сюда перекидывем уже заполненные поля !!!
                return $this->render ("humans/change", array(
                    'out_edit' => $out_edit , //передаем данные строки из таблицы objects по id
                    'cities'=> $cities,
                    'sex' => self::$sex,
                    'exp_at_human' => $exp_at_human, // профессиональный опыт.
                    'o' =>$o,
                    'pics_at_human' => $pics_at_human

                ) );
            }

            if (mb_substr($p,0,6) === 'Delnew')
            {
                $k = (int) mb_substr($p,6); //номер в массиве для удаления

                array_splice($object_data['orgname'],$k,1);
                array_splice($object_data['city_id_work'],$k,1);
                array_splice($object_data['hold_position'],$k,1);
                array_splice($object_data['start_day'],$k,1);
                array_splice($object_data['end_day'],$k,1);
                array_splice($object_data['job_text'],$k,1);
                System::set_message('form',$object_data);
                $o = $object_data['o'] -1;
                return $this->render ("humans/change", array(
                    'out_edit' => $out_edit , //передаем данные строки из таблицы objects по id
                    'cities'=> $cities,
                    'sex' => self::$sex,
                    'exp_at_human' => $exp_at_human, // профессиональный опыт.
                    'o' =>$o,
                    'pics_at_human' => $pics_at_human
                ) );
            }
            //работа с картинками

            if ($p === 'add_pic') // обработчик картинок
            {

                $file = array();
                $picture = array();
                $file = $_FILES['picture']; /// загружаем картинки в массив $file
                if ($file['name'][0] !== '') // начинаем, если первая картинка с непустым названием
                {
                    if ( !Pic::isPics_double($file['name'],$id) )  // если есть дубли - бросаем всю загрузку
                    {
                        if (image_upload($out_edit->id_human, $file)) // тут надо загрузить реально на диск
                        {
                            $picture = form_pic_name($file['name'], $out_edit->id_human); // это должен быть массив названий  картинок

                            foreach ($picture as $key => $value) // здесь записываем в базу данных
                            {
                                Pic::addpic($picture[$key],$out_edit->id_human); //добавляем в таблицу pic - информацию о картинке
                            }

                            System::set_message('success', "Фотографии добавлены успешно*");
                            header('Location: /humans/change/'.$id);
                            die();
                        }
                        else
                        {
                            System::set_message('error', "Ошибка : При заливке фотографий на диск что-то пошло не так.");
                            header('Location: /humans/change/'.$id);
                            die();
                        }
                    }
                    else
                    {
                        System::set_message('error', "Ошибка : Обнаружены дубли фотографий к этому резюме");
                        header('Location: /humans/change/'.$id);
                        die();
                    }
                }
                else
                {
                    System::set_message('error', " Ошибка : Вы не прикрепили файлы для загрузки.");
                    header('Location: /humans/change/'.$id);
                    die();
                }

            }

            if ($p === 'Delete_pic') // обработчик картинок
            {

                $pic_del_id =  (int) strip_tags($_POST['pic_del_id_pic']); // id_pic  в `pics`
                $pic_name = (string) strip_tags($_POST['pic_del_name']); // Название удаляемой картинки

                $pic_num = count ($pics_at_human); // Количество картинок у объекта

                if ( ($pic_num > 1) && ($pic_name !== 'default.jpg') ) // если больше одной - можно удалять.
                {
                    if ( del_file($pic_name) ) // удаляем физически файл через функцию в pic_helper;
                    {
                        // удаляем из таблицы картинок
                        if (Pic::del_by_id($pic_del_id))
                        {
                            System::set_message('success', "Картинку {$pic_name} удалили успешно");
                            header("Location: /humans/change/" . $id);
                            die();
                        }
                        else
                        {
                            System::set_message('error', " Ошибка : Файл удалился с сервера, а в таблице картинок что-то пошло не так.");
                        }
                    }
                    else
                    {
                        System::set_message('error', " Ошибка : Файл не удалился на хостинге, операция прервана.");
                    }
                }
                else
                {
                    if ( ($pic_num > 1) && ($pic_name === 'default.jpg') )
                    {
                        // удаляем из таблицы картинок
                        if ( Pic::del_by_id($pic_del_id) )
                            {
                                System::set_message('success', "Картинку {$pic_name} удалили успешно");
                                header("Location: /humans/change/" . $id);
                                die();
                            }
                            else
                            {
                                System::set_message('error', " Ошибка : Файл удалился с сервера, а в таблице картинок что-то пошло не так");
                            }
                    }
                    else
                    {
                        System::set_message('error', " Ошибка : Нельзя оставить резюме без фотографий :)");
                    }
                }
                // а если фотография одна
                
            }

        }

        return $this->render ("humans/change", array(
            'out_edit' => $out_edit , //передаем данные строки из таблицы objects по id
            'cities'=> $cities,
            'sex' => self::$sex,
            'exp_at_human' => $exp_at_human, // профессиональный опыт.
            'o' =>$o,
            'pics_at_human' => $pics_at_human

            ) );
    }

    public function humans_delete ($id)
    {
        if ((int) System::get_user()->role !== User::ROLE_ADMIN )
        {
            header("Location: /auth/login");
            die();
        }

        $out = new Human ($id);

        if (!$out->is_loaded() )
        {
            System::set_message('error','Ошибка : Резюме не удалось найти по его идентификатору');
            header("Location: /humans/list");
            die();
        }

        if (count($_POST) > 0)
        {
            $p = strip_tags($_POST['__action']);
            if ($p === 'delete')
            {
                $out->delete();
                if ($out->error === success) {
                    System::set_message('success','Резюме успешно удалено'); // передаем в лейаут через session сообщение об удачности или неудачности выполнения операции
                    header ('Location: /humans/list');
                    die();
                }
                else
                {
                    System::set_message('error','Ошибка , с удалением резюме id='.$id.'  что-то пошло не так');
                    header ('Location: /humans/list');
                    die();
                }
            }
        }
        return $this->render("humans/delete",array( 'out' => $out ) );
    }

    public function humans_list ()
    {
        $out = Human::all();
        System::clear_session();
        return $this->render ("humans/index",array( 'out' => $out, 'sex' => self::$sex) );
    }




}

