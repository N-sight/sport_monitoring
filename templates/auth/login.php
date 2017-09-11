<?php

?>

<div class="col-md-4 col-md-offset-4">
    <div class="login-panel panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Авторизация</h3>
        </div>

        <div class="panel-body">
            <form role="form" method="post" action="">
                <input type="hidden" name="__action" value="login"/>
                <fieldset>
                    <div class="form-group">
                        <input class="form-control" placeholder="Имя пользователя"  name="username" type="text" autofocus>
                    </div>
                    <div class="form-group">
                        <input class="form-control" type="password" placeholder="Пароль" name="password">
                    </div>
                    <div class="checkbox">
                        <label>
                            <input name="remember" type="checkbox" value="Remember Me">Remember Me
                        </label>
                    </div>
                    <!-- Change this to a button or input when using this as a form -->
                    <!--<a href="index.html" class="btn btn-lg btn-success btn-block">Login</a>-->
                    <button class="btn btn-success" type="submit">Войти</button>
                </fieldset>
            </form>
        </div>
    </div>
</div>
