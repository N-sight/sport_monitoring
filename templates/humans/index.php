<?
/* @var $out array[]*/
/* @var $sex array[]*/

$title = "Список резюме. ".title_const;
//var_dump($out);
?>
<div class="page-header">
    <h3>Список кандидатов</h3>
</div>

<div class="row">

    <a class="btn btn-success f_right" href="/humans/add/">
        <i class="fa fa-plus-square fa-lg"></i> Добавить резюме</a>

    <table class="table table-striped">
        <thead>
        <tr>
            <td>&nbsp;#&nbsp;</td>
            <td>&nbsp;Фамилия&nbsp;</td>
            <td>&nbsp;Имя&nbsp;</td>
            <td>&nbsp;День рождения&nbsp;</td>
            <td>&nbsp;Должность&nbsp;</td>
            <td>&nbsp;Зарплата&nbsp;</td>
            <td>&nbsp;Город&nbsp;</td>
            <td></td>
        </tr>
        </thead>
        <tbody>
        <!-- вывод здесь -->
        <?php
        if ( count ($out) == 0 )
        {
            echo ("<tr><td colspan=\"8\">Пока нет ни одной записи. Вы можете <a href=\"/humans/add/\" >добавить</a> первую </td></tr>");
        }
        else {
            foreach ($out as $a) {
                ?>
                <tr>
                    <td><?=$a->id_human?></td>
                    <td><?=$a->last_name?></td>
                    <td><?=$a->name?></td>
                    <td><?=$a->b_day?></td>
                    <td><?=$a->position?></td>
                    <td><?=$a->salary?></td>
                    <td><?=$a->city->title?></td>
                    <td>
                        <a class="btn btn-primary" target="_blank" href="/humans/article/<?=$a->id_human?>"><i class="fa fa-binoculars fa-lg"></i></a>
                        <a class="btn btn-success" href="/humans/change/<?=$a->id_human?>"><i class="fa fa-pencil-square-o fa-lg"></i></a>
                        <a class="btn btn-danger" href="/humans/delete/<?=$a->id_human?>"><i class="fa fa-trash-o fa-lg"></i></a>
                    </td>
                </tr>
                <?
            }
        }
        ?>
        </tbody></table>
    <a class="btn btn-success f_right" href="/humans/add/"><i class="fa fa-plus-square fa-lg"></i> Добавить резюме</a>

</div>
<!-- /.row -->
