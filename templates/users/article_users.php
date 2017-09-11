<?
/* @var $out array[]*/
$title = "Информация о пользователе".$out->username.". ".title_const;
//var_dump($out);
?>
<div class="page-header">
    <h3>Информация об пользователе: &laquo;<?=$out->username?>&raquo;</h3>
</div>
<div class="row">
    <div class="col-lg-12 col-md-12">
        <?php

        if ( $out->error = success ) // здесь вывод
        {
            $html_in = "<table class=\"table table-striped\"><thead><tr><td>&nbsp;Идентификатор&nbsp;</td><td>&nbsp;Тип пользователя&nbsp;</td><td>Имя пользователя</td></tr></thead><tbody>";
            $html_out = "</tbody></table>";
            echo $html_in;
            echo("<tr><td>".$out->id."</td><td>" . $out->role. "</td><td>".$out->username."</td></tr>");
            echo $html_out;
        }

        echo "<a class=\"btn btn-success\" href=\"/users/change/".$out->id."\"><i class=\"fa fa-pencil-square-o fa-lg\"></i>Редактировать</a>";
        echo "<span>&nbsp;</span>";
        echo "<a class=\"btn btn-danger\" href=\"/users/delete/".$out->id."\"><i class=\"fa fa-trash-o fa-lg\"></i>Удалить</a>";

        ?>

    </div>
    <!-- /.col-lg-12 -->

</div>
<!-- /.row -->
