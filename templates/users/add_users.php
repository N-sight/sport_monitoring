<?
$title = "Добавление пользователя".". ".title_const;
?>
<div class="page-header">
    <h3>Добавление нового пользователя</h3>
</div>

<div class="row">
    <div class="col-lg-12 col-md-12">
        <form action="" class="form-horizontal" method="post">
            <input type="hidden" VALUE="add" name="__action">

            <div class="form-group">
                <label for="in_type" class="col-md-4 col-lg-4 control-label">Выберете тип пользователя </label>
                <div class="col-md-8 col-lg-8">
                    <select name="role" class="form-control">
                        <?
                            foreach ($roles as $k => $v) {
                                echo "<option value=" . $k . ">" . $v . "</option>";
                            }
                        ?>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="in_name" class="col-md-4 col-lg-4 control-label">Введите логин пользователя </label>
                <div class="col-md-8 col-lg-8">
                    <input type="text" class="form-control" id="in_name" name="name" placeholder="...ваш логин">
                </div>
            </div>

            <div class="form-group">
                <label for="in_pass" class="col-md-4 col-lg-4 control-label">Введите пароль </label>
                <div class="col-md-8 col-lg-8">
                    <input type="password" class="form-control" id="in_pass" name="pass" placeholder="...ваш пароль">
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-offset-4 col-md-8 col-lg-offset-4 col-lg-8">
                    <button type="submit" class="btn btn-success" name="submitAdd">Добавить</button>
                    
                    <a class="btn btn-default" href="/users/list">Вернуться назад</a>
                </div>
            </div>
        </form>

    </div>
    <!-- /.col-lg-12 -->

</div>
<!-- /.row -->