<h3>Подтверждаете удаление &laquo;<? echo $out_del->username ?>&raquo;?</h3>
<form action="" method="post">
    <input type="hidden" name="__action" value="delete">
    <input type="hidden" name="id" value="<? echo $out_del->id; ?>">
    <button class="btn btn-danger" type="submit">Да, удалить</button>
    <a class="btn btn-default" href="/users/list">Отмена</a>
</form>