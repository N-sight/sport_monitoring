<?
/* @var $out array[]*/
$name = (string) $out->name.' '.$out->last_name;
$title = "Удаление объекта ".$name.". ".title_const;
?>
<div class="page-header">
    <h3>Удаление резюме: <?=$name?></h3>
</div>

<div class="row">
    <div class="col-lg-12">
         <h2>Подтверждаете удаление  резюме &laquo;<?= $name ?>&raquo;?</h2>
        <form action="" method="post">
            <input type="hidden" name="__action" value="delete">
            <input type="hidden" name="id" value="<?= $out->id_human?>">
            <button class="btn btn-danger" type="submit">Да, удалить</button>
            <a class="btn btn-default" href="/humans/list">Отмена</a>
        </form>

    </div>
    <!-- /.col-lg-12 -->

</div>
<!-- /.row -->
