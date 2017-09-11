<?
/* @var $out_edit array[]*/
$title = "Редактирование имени пользователя ".$out_edit->username.". ".title_const;
?>
<div class="page-header">
    <h3>Редактирование имени пользователя: &laquo;<?=$out_edit->username?>&raquo;</h3>
</div>
<form action="" class="form-horizontal" method="post">

    <div class="form-group">
        <label for="in_name" class="col-md-4 control-label">Измените имя пользователя </label>
        <div class="col-md-8">
            <input type="text" class="form-control" id="in_name" name="name" value="<? echo $out_edit->username?>">
        </div>
    </div>
    
    <input type="hidden" value="<? echo $out_edit->id?>" name="id">
    <input type="hidden" value="edit" name="__action">
    <div class="form-group">
        <div class="col-md-offset-4 col-md-8">
            <button type="submit" class="btn btn-warning" name="submitChange">Изменить данные</button>
            <a href="/users/list">
                <button type="button" class="btn btn-default">не менять, вернуться на страницу просмотра</button>
            </a>
        </div>
    </div>
</form>
<span class="brown">После изменения своего имени пользователя необходимо перелогиниться</span>
