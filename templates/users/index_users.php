<?
/* @var $out array[]*/
$title = "Список пользователей. ".title_const;
?>
<div class="page-header">
    <h3>Пользователи:</h3>
</div>
<div class="row">
    <div class="col-lg-12">

        <a class="btn btn-success f_right" href="/users/add">
            <i class="fa fa-plus-square fa-lg"></i><i class="fa fa-asterisk"></i> Добавить пользователя </a>

        <table class="table table-striped"><thead><tr><td>&nbsp;# &nbsp;</td><td>&nbsp;Имя пользователя&nbsp;</td><td>&nbsp;Роль&nbsp;</td><td></td></tr></thead>
            <tbody>
            <!-- вывод здесь -->
            <?php

            if (count($users)>0)
            {
                foreach ($users as $a)
                {
                    echo("<tr><td>" . $a->id . "</td><td>" . $a->username . "</td><td>" . User::$roles[$a->role] . "</td><td><a class=\"btn btn-primary\" href=\"/users/article/" .$a->id. "\">
                              <i class=\"fa fa-binoculars fa-lg\"></i></a>
                              <a class=\"btn btn-success\" href=\"/users/change/" .$a->id. "\"><i class=\"fa fa-pencil-square-o fa-lg\"></i><i class=\"fa fa-asterisk\"></i></a>
                              <a class=\"btn btn-danger\" href=\"/users/delete/" .$a->id. "\"><i class=\"fa fa-trash-o  fa-lg\"></i><i class=\"fa fa-asterisk\"></i></a></td></tr>");
                }
            }
            else
            {
                echo ("<tr><td colspan=\"4\">Пока нет ни одной записи. Вы можете <a href=\"/users/add\">добавить</a> первую </td></tr>");
            }
            ?>
</tbody></table>
<a class="btn btn-success f_right" href="/users/add">
    <i class="fa fa-plus-square fa-lg"></i><i class="fa fa-asterisk"></i> Добавить пользователя</a>
</div>
<!-- /.col-lg-12 -->

</div>
<!-- /.row -->
<div class="row">
    <i class="fa fa-asterisk"></i><span> Для удаления, редактирования и добавления нужны права Администратора</span>
</div>