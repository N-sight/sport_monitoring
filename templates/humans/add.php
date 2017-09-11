<?
/* @var $cities array[]*/
/* @var $o int*/

$title = "Добавление нового резюме. ".title_const;
$form = System::get_message('form');

$pics = System::get_message('pics');
System::set_message('pics',$pics);
var_dump($pics);

if (isset ($form['b_day']))
{
    $b_day = $form ['b_day'];
}
else
{
    $b_day = '1980-07-15';
}

for ($i=0;$i<$o;$i++)
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
        $form['city_id_work'][$i] = 0;
        $form['prof_id'][$i] = 0;
        $form['hold_position'][$i] = '';
        $form['start_day'][$i] = '';
        $form['end_day'][$i] = '';
        $form['just_now_flag'][$i] = '0';
        $form['job_text'][$i] = '';
    }
}

?>
<div class="page-header">
    <h3>Добавление нового резюме</h3>
</div>

<form action="" method="post" enctype="multipart/form-data">
    <div class="row">
        <div class="col-lg-offset-4 col-lg-8  col-md-offset-4 col-md-8">
            <table class="table">
                <tr>
                    <td><i class="fa fa-bookmark"></i> Фамилия :</td>
                    <td>
                        <input type="text" class="form-control" name="last_name" value = "<?=$form['last_name']?>" placeholder="...Фамилия">
                    </td>
                </tr>
                <tr>
                    <td><i class="fa fa-bookmark"></i> Имя :</td>
                    <td>
                        <input type="text" class="form-control" name="name" value = "<?=$form['name']?>" placeholder="...Имя">
                    </td>
                </tr>
                <tr>
                    <td><i class="fa fa-bookmark"></i> Отчество :</td>
                    <td>
                        <input type="text" class="form-control" name="sur_name" value = "<?=$form['sur_name']?>" placeholder="...Отчество">
                    </td>
                </tr>
                <tr>
                    <td><i class="fa fa-bookmark"></i> Дата рождения :</td>
                    <td>
                        <input type="text" class="tcal form-control" name="b_day" min="1920-01-01" max="2000-01-01" value="<?=$b_day?>">
                    </td>
                </tr>
                <tr>
                    <td><i class="fa fa-bookmark"></i> Пол :</td>
                    <td>
                        <select name="sex" class="form-control">
                            <option <? if ($form['sex'] === 1) echo 'selected'?> value = "1">Мужчина</option>
                            <option <? if ($form['sex'] === 0) echo 'selected'?> value = "0">Женщина</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><i class="fa fa-bookmark"></i> Город :</td>
                    <td>
                        <select name="city_id" class="form-control">
                            <?
                            foreach ($cities as $c => $v)
                            {
                                if ($form['city_id'] === $v->id)
                                {
                                    echo "<option selected value=" . $v->id . ">" . $v->title . "</option>";
                                }
                                else {
                                    echo "<option value=" . $v->id . ">" . $v->title . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><i class="fa fa-newspaper-o"></i> Желаемая должность:</td>
                    <td>
                        <input type="text" class="form-control" name="position" value = "<?=$form['position']?>" placeholder="...Должность">
                    </td>
                </tr>
                <tr class="hidden">
                    <td></td>
                    <td><input type="hidden" VALUE="0" name="prof_area1"> <!-- todo --></td>
                </tr>
                <tr class="hidden">
                    <td></td>
                    <td><input type="hidden" VALUE="0" name="prof_area2"> <!-- todo --></td>
                </tr>
                <tr class="hidden">
                    <td></td>
                    <td><input type="hidden" VALUE="0" name="prof_area3"> <!-- todo --></td>
                </tr>
                <tr>
                    <td><i class="fa fa-money"></i> Зарплата:</td>
                    <td>
                        <input type="text" class="form-control" name="salary" value = "<?=$form['salary']?>" placeholder="...Зарплата">
                    </td>
                </tr>
                <tr>
                    <td><i class="fa fa-camera-retro"></i> Загрузите вашу фотографию:</td>
                    <td>
                        <small><input type="file" name="picture[]" multiple accept="image/*"></small>
                    </td>
                </tr>
                <?php
                    if (isset($pics['name'][0]))
                    {?>
                        <tr>
                            <td><i class="fa fa-camera"></i> Загруженные изображения:</td>
                            <td>
                                <?php
                                    for ($m=0;$m<count($pics['name']);$m++)
                                    {
                                        writeln($pics['name'][$m]);
                                    }
                                ?>
                            </td>
                        </tr>
                <?php
                    }
                ?>
                <tr>
                    <td>
                    </td>
                    <td><h5 class="red">Все поля обязательны для заполнения</h5></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="dummyH10" style="background-color: grey"></div><div class="dummyH10" style="background-color: grey"></div>

    <div class="row">
        <div class="page-header">
            <h4>Профессиональный опыт :</h4>
        </div>
          <table>
            <!--                 ПОЛЯ ПРОФЕССИОНАЛЬНОГО ОПЫТА-->
            <? for ($i=0;$i<$o;$i++) {?>
                <tr>
                    <td><i class="fa fa-bookmark"></i> Организация :</td>
                    <td>
                        <input type="text" class="form-control" name="orgname[<?=$i?>]" value = "<?=$form['orgname'][$i]?>" placeholder="...Организация">
                    </td>
                </tr>
                <tr>
                    <td><i class="fa fa-bookmark"></i> Город :</td>
                    <td>
                        <select name="city_id_work[<?=$i?>]" class="form-control"> <!--ОБРАТИ ВНИМАНИЕ НА NAME-->
                            <?
                            foreach ($cities as $c => $v)
                            {
                                if (isset ($form['city_id_work'][$i]) )
                                {
                                    if ($form['city_id_work'][$i] == $v->id)
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
                    <td><input type="hidden" VALUE="0" name="prof_id[<?=$i?>]"></td>

                </tr>
                <tr>
                    <td><i class="fa fa-bookmark"></i> Должность :</td>
                    <td>
                        <input type="text" class="form-control" name="hold_position[<?=$i?>]" value = "<?=$form['hold_position'][$i]?>" placeholder="...Должность">
                    </td>
                </tr>
                <tr>
                    <td><i class="fa fa-bookmark"></i> Дата начала:</td>
                    <td>
                        <input type="text" class="tcal form-control" value ="<?=$start_day[$i]?>" name="start_day[<?=$i?>]">
                    </td>
                </tr>
                <tr>
                    <td><i class="fa fa-bookmark"></i> Дата конца:</td>
                    <td>
                        <input type="text" class="tcal form-control" value ="<?=$end_day[$i]?>" name="end_day[<?=$i?>]">
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
                            var txt_area<?=$i?> =  "<?=$form['job_text'][$i]?>";
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
        </table>
    </div>


    <div class="row">
        <div class="dummyH10"></div>
        <input type="hidden" VALUE="<?=$i?>" name="o">  <!--передаем число итераций профессонального опыта-->
        <button class="btn btn-success" type="submit" VALUE="exp" name="__action">Добавить место работы</button>
        <button class="btn btn-primary" type="submit" VALUE="add" name="__action">Cохранить </button>
        <a class="btn btn-default" href="/humans/list/">Вернуться назад</a>
    </div>
</form>

