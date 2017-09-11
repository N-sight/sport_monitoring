<?
/* @var $out_view_busy array[]*/
$title = "Удаление пользователя ".$out_del->username.". ".title_const;
//var_dump($out_del);
?>
<div class="page-header">
    <h3>Удаление пользователя: <?=$out_del->username?></h3>
</div>

<div class="row">
    <div class="col-lg-12">
        <?php
        if ($out_del->error == repeats )
        {
            echo('<span class="brown">Предупреждение: Нельзя удалить пользователя под которым вы вошли в систему. </span>');

            echo "<a class=\"btn btn-default\" href=\"/users/list\">К списку пользователей </a>";
        }

        if ($out_del->error == first ) {
            echo('<span class="brown">Предупреждение : Эта запись является зарезервированной специально. Нельзя оставлять систему без первого пользователя.<br><br> </span>');
            echo "<a class=\"btn btn-default\" href=\"/users/list\">К списку пользователей </a><span>&nbsp;</span> ";
            echo "<a class=\"btn btn-warning\" href=\"/users/change/2\">Изменить название</a>";

        }

        if ($out_del->error == void )
        {
            echo "<p class=\"blue\"> Запись свободна от ограничений<p>";
            require_once ('delete_users_void.php');
        }
        ?>


    </div>
    <!-- /.col-lg-12 -->

</div>
<!-- /.row -->
