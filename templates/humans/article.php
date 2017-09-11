<?
/* @var $out array[]*/
/* @var $exp_at_human array[]*/
/* @var $pics_at_human array[]*/
/* @var $sex array[]*/
/* @var $cities array[]*/

$name = (string) $out->name.' '.$out->last_name;
$title = "Просмотр резюме ".$name.". ".title_const;


$num_pics =count ($pics_at_human); // Количество картинок у объекта.
$num_left = round ($num_pics/2); // Количество левых колонок
$num_right = $num_pics - $num_left; //Количество правых колонок
?>

<div class="page-header">
    <h3>Резюме: &laquo;<?=$name?>&raquo;</h3>
</div>

<div class="row">
    <div class="col-lg-1 col-md-1"><!--отступ--></div>
    <div class="col-lg-2 col-md-2">
        <?
        for ($i=1; $i<=$num_left; $i++)
        {
            $ctr = 2*$i-2;
            $n = $pics_at_human[$ctr];
            echo "<a href=\"../../images/".$n['picture']."\" class=\"flipLightBox\">
                        <img  class=\"image-round\" src=\"../../images/".$n['picture']."\" alt=\"".$n['picture']."\" height=\"230\" width=\"230\"><span>Галерея объекта:" . $name . "</span>
                      </a><div class=\"dummyH10\"></div>";
        }
        ?>
    </div>
    <div class="col-lg-2 col-md-2">
        <?
        for ( $i=1; $i<=$num_right; $i++)
        {
            $ctr = 2*($i)-1;
            $n = $pics_at_human[$ctr];
            echo "<a href=\"../../images/" . $n['picture'] . "\" class=\"flipLightBox\">
                        <img class=\"image-round\" src=\"../../images/" . $n['picture'] . "\" alt=\"" . $n['picture'] . "\" height=\"230\" width=\"230\"><span>Галерея объекта:" . $name . "</span>
                      </a><div class=\"dummyH10\"></div>";
        }
        ?>
    </div>

    <div class="col-lg-1 col-md-1"><!--отступ--></div>

    <div class="col-lg-6 col-md-6">
        <table class="table">
            <tr>
                <td><i class="fa fa-user"></i> Фамилия:</td>
                <td><?=$out->last_name?></td>
            </tr>
            <tr>
                <td><i class="fa fa-user"></i> Имя:</td>
                <td><?=$out->name?></td>
            </tr>
            <tr>
                <td><i class="fa fa-user"></i> Отчество:</td>
                <td><?=$out->sur_name?></td>
            </tr>
            <tr>
                <td><i class="fa fa-globe"></i> Город:</td>
                <td><?=$out->city->title?></td>
            </tr>
            <tr>
                <td><i class="fa fa-thumb-tack"></i> Дата рождения:</td>
                <td><?=$out->b_day?></td>
            </tr>

            <tr>
                <td><i class="fa fa-heart"></i> Пол:</td>
                <td><?=$sex[$out->sex]?></td>
            </tr>
            <tr>
                <td><i class="fa fa-newspaper-o"></i> Должность:</td>
                <td><?=$out->position?></td>
            </tr>
            <tr>
                <td><i class="fa fa-money"></i> Зарплата:</td>
                <td><?=$out->salary?></td>
            </tr>
        </table>
    </div>
    <!-- /.col-lg-6 col-md-6 -->
</div>
<!-- /.row -->

<div class="row">
    <div class="page-header">
        <h4>Профессиональный опыт:</h4>
    </div>
    <table>
        <!--                 ПОЛЯ ПРОФЕССИОНАЛЬНОГО ОПЫТА-->
        <? for ($i=0;$i<count($exp_at_human);$i++)
        {?>
            <tr class="tab_margin">
                <td><i class="fa fa-bookmark"></i> Организация :</td>
                <td>
                    <?=$exp_at_human[$i]['org_name']?>
                </td>
            </tr>
            <tr>
                <td><i class="fa fa-bookmark"></i> Город :</td>
                <td>
                    <?=$cities[$exp_at_human[$i]['region']]->title?>
                </td>
            </tr>
            <tr class="hidden">  <!--HIDDEN-->
                <td></td>
                <td><!--profid--></td>
            </tr>
            <tr>
                <td><i class="fa fa-bookmark"></i> Должность :</td>
                <td>
                    <?=$exp_at_human[$i]['hold_position']?>
                </td>
            </tr>
            <tr>
                <td><i class="fa fa-bookmark"></i> Дата начала:</td>
                <td>
                    <?=$exp_at_human[$i]['start_date']?>
                </td>
            </tr>
            <tr>
                <td><i class="fa fa-bookmark"></i> Дата конца:</td>
                <td>
                    <?=$exp_at_human[$i]['end_date']?>
                </td>
            </tr>
            <tr class="hidden">  <!--HIDDEN-->
                <td></td>
                <td><!-- just  --></td>

            </tr>
            <tr>
                <td><i class="fa fa-bookmark"></i> Описание деятельности : </td>
                <td>
                    <?=$exp_at_human[$i]['job_text']?>
                </td>
            </tr>
            <tr>
                <td><p>&nbsp;</p><hr/></td>
            </tr>
            <?
        }
        ?>
    </table>
</div>

<?php
echo "<a class=\"btn btn-success\" href=\"/humans/change/".$out->id_human."\"><i class=\"fa fa-pencil-square-o fa-lg\"></i> Редактировать</a>";
echo "<span>&nbsp;</span>";
echo "<a class=\"btn btn-danger\" href=\"/humans/delete/".$out->id_human."\"><i class=\"fa fa-trash-o fa-lg\"></i> Удалить</a>";
echo "<a class=\"btn btn-default\" href=\"/humans/list\">Вернуться на страницу списка</a>";
?>


