<?
/*  @var $out_edit object */
/*  @var $sex array */
/*  @var $cities array */
/*  @var $exp_at_human array[]*/
/*  @var $pics_at_human array[]*/
/*  @var $o int*/

$name = (string) $out_edit->name.' '.$out_edit->last_name;
$title = "Редактирование резюме ".$name.". ".title_const;
$form = System::get_message('form');
$j = count ($exp_at_human);
for ($i=$j;$i<($o+$j);$i++)
{
    if (isset ($form['start_day'][$i]))
    {
        $start_day[$i] = $form['start_day'][$i];
    }
    else
    {
        $start_day[$i] = "2015-12-31";
    }
    if (isset ($form['end_day'][$i]))
    {
        $end_day[$i] = $form['end_day'][$i];
    }
    else
    {
        $end_day[$i] = "2016-06-30";
    }

    if (!isset ($form['orgname'][$i]))
    {
        $form['orgname'][$i] = '';
        $form['city_id_work'][$i] = $out_edit->city_id;
        $form['prof_id'][$i] = 0;
        $form['hold_position'][$i] = '';
        $form['start_day'][$i] = '';
        $form['end_day'][$i] = '';
        $form['just_now_flag'][$i] = '0';
        $form['job_text'][$i] = '';
    }

}
/*
$c = count($exp_at_human);
for ($k=$c;$k<($c+$o);$k++)
{
    if (!isset ($form['orgname'][$k]))
    {
        $form['orgname'][$k] = '';
        $form['city_id_work'][$k] = $out_edit->city_id;
        $form['prof_id'][$k] = 0;
        $form['hold_position'][$k] = '';
        $form['start_day'][$k] = '';
        $form['end_day'][$k] = '';
        $form['just_now_flag'][$k] = '0';
        $form['job_text'][$k] = '';
    }
}*/

$num_pics =count ($pics_at_human); // Количество картинок у объекта.
$num_left = round ($num_pics/2); // Количество левых колонок
$num_right = $num_pics - $num_left; //Количество правых колонок


?>

<div class="page-header">
    <h3>Редактирование резюме: &laquo;<?=$name?>&raquo;</h3>
</div>


    <div class="row"> <!-- Тело всех изменений -->
        <div class="col-lg-1 col-md-1"></div> <!-- Отступ -->
        <div class="col-lg-2 col-md-2">
            <h4><i class="fa fa-camera-retro"></i> Картинки</h4>
            <?
            for ( $i=1; $i<=$num_left; $i++)
            {
                $k = 2*($i-1);
                echo "<form action=\"\" method=\"post\">";
                echo "<input type=\"hidden\" name=\"__action\" value=\"Delete_pic\">";
                echo "<input type=\"hidden\" name=\"pic_del_id_pic\" value=\"" . $pics_at_human[$k]['id_pic'] . "\">";
                echo "<input type=\"hidden\" name=\"pic_del_name\" value=\"" . $pics_at_human[$k]['picture'] . "\">";

                echo "<a href=\"../../images/".$pics_at_human[$k]['picture']."\" class=\"flipLightBox\">
                            <img  class=\"image-round\" src=\"../../images/".$pics_at_human[$k]['picture']."\" alt=\"".$pics_at_human[$k]['picture']."\" height=\"230\" width=\"230\"><span>Фотографии к резюме:.$name.</span>
                          </a>";
                echo "<button class=\"btn btn-warning btn-xs center-block\">" . $pics_at_human[$k]['picture'] . "&nbsp;<i class=\"fa fa-times\"></i></button>
                <div class=\"dummyH10\"></div>";
                echo "</form>";
            }
            ?>
            <h4><i class="fa fa-file-image-o"></i> Загрузить еще картинок:</h4>
            <div class="dummyH10"></div>
            <form action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="__action" value="add_pic">
                <input type="hidden" name="id_obj" value="<?=$out_edit->id_human?>">
                <input type="file" name="picture[]" multiple accept="image/*">
                <div class="dummyH10"></div>
                <button type="submit" class="btn btn-success btn-sm center-block" name="addpic"> <i class="fa fa-plus"></i> Загрузить</button>
            </form>
            <small>*Последнюю картинку удалить нельзя</small>
        </div>
        <div class="col-lg-2 col-md-2">
            <h4><i class="fa"></i></h4>
            <?
            for ( $i=1; $i<=$num_right; $i++)
            {
                $k = 2*($i-1)+1 ;
                echo "<form action=\"\" method=\"post\">";
                echo "<input type=\"hidden\" name=\"__action\" value=\"Delete_pic\">";
                echo "<input type=\"hidden\" name=\"pic_del_id_pic\" value=\"" . $pics_at_human[$k]['id_pic'] . "\">";
                echo "<input type=\"hidden\" name=\"pic_del_name\" value=\"" . $pics_at_human[$k]['picture'] . "\">";

                echo "<a href=\"../../images/".$pics_at_human[$k]['picture']."\" class=\"flipLightBox\">
                            <img class=\"image-round\" src=\"../../images/".$pics_at_human[$k]['picture']."\" alt=\"".$pics_at_human[$k]['picture']."\" height=\"230\" width=\"230\"><span>Фотографии к резюме:.$name.</span>
                          </a>";
                echo "<button class=\"btn btn-warning btn-xs center-block\">" . $pics_at_human[$k]['picture'] . "&nbsp;<i class=\"fa fa-times\"></i></button><div class=\"dummyH10\"></div>";
                echo "</form>";
            }
            ?>
        </div>
        <div class="col-lg-1 col-md-1 "></div> <!-- Отступ -->

        <div class="col-lg-6 col-md-6 ">  <!--основные поля-->
                <form action="" method="post">
                    <input type="hidden" name="__action" value="edit">
                    <input type="hidden" value="<?=$out_edit->id_human?>" name="id_human">

                    <table class="table">
                        <tr>
                            <td><i class="fa fa-user"></i> Фамилия:</td>
                            <td><div class="form-group"><input style="width: 185px;" type="text" class="form-control" name="last_name" value="<?=$out_edit->last_name?>"/></div></td>
                        </tr>
                        <tr>
                            <td><i class="fa fa-user"></i> Имя:</td>
                            <td><div class="form-group"><input style="width: 185px;" type="text" class="form-control" name="name" value="<?=$out_edit->name?>"/></div></td>
                        </tr>
                        <tr>
                            <td><i class="fa fa-user"></i> Отчество:</td>
                            <td><div class="form-group"><input style="width: 185px;" type="text" class="form-control" name="sur_name" value="<?=$out_edit->sur_name?>"/></div></td>
                        </tr>
                        <tr>
                            <td><i class="fa fa-globe"></i> Город:</td>
                            <td>
                                <div class="form-group">
                                    <select name="city_id" class="form-control" style="width: 185px;">
                                        <?
                                        foreach ($cities as $c => $value)
                                        {
                                            if ( $value->id == $out_edit->city_id) // выбранный город
                                            {
                                                echo "<option value=" . $value->id . " selected >" . $value->title . "</option>";
                                            }
                                            else
                                            {
                                                echo "<option value=" . $value->id . ">" . $value->title . "</option>";
                                            }

                                        }
                                        ?>
                                    </select>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><i class="fa fa-thumb-tack"></i> Дата рождения:</td>
                            <td><div class="form-group">
                                    <input style="width: 185px;" type="text" class="tcal form-control" name="b_day" min="1920-01-01" max="2000-01-01" value="<?=$out_edit->b_day?>">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><i class="fa fa-heart"></i> Пол<small><small>(на случай кто передумает)</small></small>:</td>
                            <td>
                                <div class="form-group">
                                    <select name="sex" class="form-control" style="width: 185px;">
                                        <?
                                        foreach ($sex as $i => $val)
                                        {
                                            if ( $i == $out_edit->sex) // выбранный пол
                                            {
                                                echo "<option value=" . $i . " selected >" . $val . "</option>";
                                            }
                                            else
                                            {
                                                echo "<option value=" . $i . ">" . $val . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><i class="fa fa-newspaper-o"></i> Должность:</td>
                            <td><div class="form-group"><input style="width: 185px;" type="text" class="form-control"  name="position" value="<?=$out_edit->position?>"/></div></td>
                        </tr>
                        <tr>
                            <td><i class="fa fa-money"></i> Зарплата:</td>
                            <td><div class="form-group"><input style="width: 185px;" type="text" class="form-control"  name="salary" value="<?=$out_edit->salary?>"/></div></td>
                        </tr>
                </table>
                    <button type="submit" class="btn btn-warning" name="submitChange">Изменить основные поля</button>
                    <a class="btn btn-default" href="/humans/list">Вернуться на страницу списка</a>
                </form>
        </div>
        <!-- /.col-lg-6 col-md-6 -->

    </div>

    <div class="row"><!-- ПРОФЕССИОНАЛЬНЫЙ ОПЫТ-->
        <div class="page-header">
            <h4>Профессиональный опыт :</h4>
        </div>
        <form action="" method="post">
            <input type="hidden" name="__action" value="exp_edit">
            <table>
                <!--ПОЛЯ ПРОФЕССИОНАЛЬНОГО ОПЫТА  ИЗ БАЗЫ ДАННЫХ-->
            <? for ($i=0;$i<count($exp_at_human);$i++) {?>
                <tr class="tab_margin">
                    <td><i class="fa fa-bookmark"></i> Организация :</td>
                    <td>
                        <input type="text" class="form-control" name="orgname[<?=$i?>]" value = "<?=$exp_at_human[$i]['org_name']?>" placeholder="...Организация">
                    </td>
                </tr>
                <tr>
                    <td><i class="fa fa-bookmark"></i> Город :</td>
                    <td>
                        <select name="city_id_work[<?=$i?>]" class="form-control"> <!--ОБРАТИ ВНИМАНИЕ НА NAME-->
                            <?
                            foreach ($cities as $c => $v)
                            {
                                if (isset ($exp_at_human[$i]['region']) )
                                {
                                    if ($exp_at_human[$i]['region'] == $v->id)
                                    {
                                        echo "<option selected value=" . $v->id . ">" . $v->title . "</option>";
                                    }
                                    else
                                    {
                                        echo "<option value=" . $v->id . ">" . $v->title . "</option>";
                                    }

                                }
                                else
                                {
                                    if ($out_edit->city_id === $v->id) // смысл в том, что
                                    {
                                        echo "<option selected value=" . $v->id . ">" . $v->title . "</option>";
                                    }
                                    else
                                    {
                                        echo "<option value=" . $v->id . ">" . $v->title . "</option>";
                                    }
                                }

                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr class="hidden">  <!--HIDDEN-->
                    <td></td>
                    <td><input type="hidden" VALUE="0" name="prof_id[<?=$i?>]"></td>

                </tr>
                <tr>
                    <td><i class="fa fa-bookmark"></i> Должность :</td>
                    <td>
                        <input type="text" class="form-control" name="hold_position[<?=$i?>]" value = "<?=$exp_at_human[$i]['hold_position']?>" placeholder="...Должность">
                    </td>
                </tr>
                <tr>
                    <td><i class="fa fa-bookmark"></i> Дата начала:</td>
                    <td>
                        <input type="text" class="tcal form-control" value ="<?=$exp_at_human[$i]['start_date']?>" name="start_day[<?=$i?>]">
                    </td>
                </tr>
                <tr>
                    <td><i class="fa fa-bookmark"></i> Дата конца:</td>
                    <td>
                        <input type="text" class="tcal form-control" value ="<?=$exp_at_human[$i]['end_date']?>" name="end_day[<?=$i?>]">
                    </td>
                </tr>
                <tr class="hidden">  <!--HIDDEN-->
                    <td></td>
                    <td><input type="hidden" VALUE="0" name="just_now_flag[<?=$i?>]"></td>

                </tr>
                <tr>
                    <td><i class="fa fa-bookmark"></i> Опишите чем вы занимались:</td>
                    <td>
                        <textarea id="desc<?=$i?>" class="form-control" rows="4" placeholder="...Опишите вашу деятельность своими словами" name="job_text[<?=$i?>]"></textarea>
                        <script>
                            var txt_area<?=$i?> =  "<?=$exp_at_human[$i]['job_text']?>";
                            document.getElementById ('desc<?=$i?>').value = txt_area<?=$i?>;
                        </script>
                    </td>
                </tr>
                <tr>
                    <td></td><td><button type="submit" VALUE="del<?=$i?>" class="btn-sm btn-adn" name="__action">Удалить эту запись об этой работе</button></td>
                </tr>
                <tr>
                    <td><p>&nbsp;</p><hr/></td>
                </tr>
                <?
            }
            ?>
                
                <!--                 ПОЛЯ ПРОФЕССИОНАЛЬНОГО ОПЫТА НОВЫЕ, НЕ ВНЕСЕННЫЕ В БД-->
                <? for ($j=$i;$j<($o+$i);$j++) {?>

                    <tr>
                        <td><?=$o?> </td><td><?=$j?></td>
                    </tr>
                    <tr>
                        <td><i class="fa fa-bookmark red"></i> Организация :</td>
                        <td>
                            <input type="text" class="form-control" name="orgname[<?=$j?>]" value = "<?=$form['orgname'][$j]?>" placeholder="...Организация">
                        </td>
                    </tr>
                    <tr>
                        <td><i class="fa fa-bookmark"></i> Город :</td>
                        <td>
                            <select name="city_id_work[<?=$j?>]" class="form-control"> <!--ОБРАТИ ВНИМАНИЕ НА NAME-->
                                <?
                                foreach ($cities as $c => $v)
                                {
                                    if (isset ($form['city_id_work'][$j]) )
                                    {
                                        if ($form['city_id_work'][$j] == $v->id)
                                        {
                                            echo "<option selected value=" . $v->id . ">" . $v->title . "</option>";
                                        }
                                        else
                                        {
                                            echo "<option value=" . $v->id . ">" . $v->title . "</option>";
                                        }

                                    }
                                    else
                                    {
                                        if ($form['city_id'] === $v->id)
                                        {
                                            echo "<option selected value=" . $v->id . ">" . $v->title . "</option>";
                                        }
                                        else
                                        {
                                            echo "<option value=" . $v->id . ">" . $v->title . "</option>";
                                        }
                                    }

                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr class="hidden">  <!--HIDDEN-->
                        <td></td>
                        <td><input type="hidden" VALUE="0" name="prof_id[<?=$j?>]"></td>

                    </tr>
                    <tr>
                        <td><i class="fa fa-bookmark"></i> Должность :</td>
                        <td>
                            <input type="text" class="form-control" name="hold_position[<?=$j?>]" value = "<?=$form['hold_position'][$j]?>" placeholder="...Должность">
                        </td>
                    </tr>
                    <tr>
                        <td><i class="fa fa-bookmark"></i> Дата начала:</td>
                        <td>
                            <input type="text" class="tcal form-control" value ="<?=$start_day[$j]?>" name="start_day[<?=$j?>]">
                        </td>
                    </tr>
                    <tr>
                        <td><i class="fa fa-bookmark"></i> Дата конца:</td>
                        <td>
                            <input type="text" class="tcal form-control" value ="<?=$end_day[$j]?>" name="end_day[<?=$j?>]">
                        </td>
                    </tr>
                    <tr class="hidden">  <!--HIDDEN-->
                        <td></td>
                        <td><input type="hidden" VALUE="0" name="just_now_flag[<?=$j?>]"></td>

                    </tr>
                    <tr>
                        <td><i class="fa fa-bookmark"></i> Опишите чем вы занимались:</td>
                        <td>
                            <textarea id="desc<?=$j?>" class="form-control" rows="4" placeholder="...Опишите вашу деятельность своими словами" name="job_text[<?=$j?>]"></textarea>
                            <script>
                                var txt_area<?=$j?> =  "<?=$form['job_text'][$j]?>";
                                document.getElementById ('desc<?=$j?>').value = txt_area<?=$j?>;
                            </script>
                        </td>
                    </tr>
                    <tr>
                        <td></td><td><button type="submit" VALUE="Delnew<?=$j?>" class="btn-sm btn-adn" name="__action">Удалить эту запись об этой работе</button></td>
                    </tr>
                    <tr>
                        <td><p>&nbsp;</p><hr/></td>
                    </tr>
                    <?
                }
                ?>

        </table>
            <input type="hidden" VALUE="<?=($j-$i)?>" name="o">  <!--передаем число итераций не внесенного профессонального опыта-->
            <button type="submit" VALUE="insert_new" class="btn btn-success" name="__action">Добавить трудовой опыт</button>
            <button type="submit" class="btn btn-warning" name="submitChange">Изменить поля опыта</button>
            <a class="btn btn-default" href="/humans/list">Вернуться на страницу списка</a>
        </form>
    </div>

