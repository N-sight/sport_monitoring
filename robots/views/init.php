<?php
$dir = 'http://'.$_SERVER['HTTP_HOST']
        .dirname($_SERVER['PHP_SELF'])
        .'/';
?>
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="multiparser">
    <meta name="author" content="n_sight">

    <title>Парсер спортивных сайтов</title>

    <!-- Bootstrap Core CSS -->
    <link href="../css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="../css/heroic-features.css" rel="stylesheet">

    <!-- Simple-Calendar -->
    <link href="../css/tcal.css" rel="stylesheet" type="text/css">

    <!-- Another CSS -->
    <link rel="stylesheet" type="text/css" href="<?=substr($dir,0,-6)?>css/option.css.php">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>

    <!-- Navigation -->
    <!--<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
            &lt;!&ndash; Brand and toggle get grouped for better mobile display &ndash;&gt;
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#">Start Bootstrap</a>
            </div>
            &lt;!&ndash; Collect the nav links, forms, and other content for toggling &ndash;&gt;
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <li>
                        <a href="#">About</a>
                    </li>
                    <li>
                        <a href="#">Services</a>
                    </li>
                    <li>
                        <a href="#">Contact</a>
                    </li>
                </ul>
            </div>
            &lt;!&ndash; /.navbar-collapse &ndash;&gt;
        </div>
        &lt;!&ndash; /.container &ndash;&gt;
    </nav>
-->
    <!-- Page Content -->
    <div class="container">

        <!-- Jumbotron Header -->
        <header class="jumbotron hero-spacer logo">
            <h2 class="text-center">Парсер спортивных сайтов</h2>
            <p class="text-center">Собираем комментарии с источников про Леонида Федуна.</p>
        </header>

        <!-- Page Features -->
        <div class="row text-center">

            <form action="../index.php"method="POST"> <!--  method="POST" action="../index.php" target="_blank"-->

                <!-- btn-group -->
                <div class="btn-group" data-toggle="buttons">

                    <div class="col-md-3 col-sm-6 hero-feature">
                        <div class="thumbnail">
                            <img src="<?=$dir?>../img/sports.png" alt="sports.ru">
                            <div class="caption">
                                <p>
                                    <label class="btn btn-primary">
                                        <input name="source" value = 'sports' type="radio"> Sports.ru
                                    </label>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6 hero-feature">
                        <div class="thumbnail">
                            <img src="<?=$dir?>../img/championat.png" alt="championat.com">
                            <div class="caption">
                                <p>
                                    <label class="btn btn-primary">
                                        <input name="source" value = 'championat' type="radio" > Championat.com
                                    </label>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6 hero-feature">
                        <div class="thumbnail">
                            <img src="<?=$dir?>../img/sovsport.png" alt="sovsport.ru">
                            <div class="caption">
                                <p>
                                    <label class="btn btn-primary">
                                        <input name="source" value = 'sovsport' type="radio"> Sovsport.ru
                                    </label>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6 hero-feature">
                        <div class="thumbnail">
                            <img src="<?=$dir?>../img/sport-express.png" alt="sport-express.ru">
                            <div class="caption">
                                <p>
                                    <label class="btn btn-primary">
                                        <input name="source" value = 'sportexpress' type="radio" > sport-express.ru
                                    </label>
                                </p>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- /btn-group -->

                <!-- btn-group -->
                <div class="btn-group" data-toggle="buttons">

                    <div class="col-md-3 col-sm-6 hero-feature">
                        <div class="thumbnail">
                            <img src="<?=$dir?>../img/eurosport.png" alt="eurosport.ru">
                            <div class="caption">
                                <p>
                                    <label class="btn btn-primary">
                                        <input name="source" value = 'eurosport' type="radio"> Eurosport.ru
                                    </label>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6 hero-feature">
                        <div class="thumbnail">
                            <img src="<?=$dir?>../img/sportbox.png" alt="news.sportbox.ru">
                            <div class="caption">
                                <p>
                                    <label class="btn btn-primary">
                                        <input name="source" value = 'sportbox' type="radio"> Sportbox**** (indev)
                                    </label>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6 hero-feature">
                        <div class="thumbnail">
                            <img src="<?=$dir?>../img/soccer.jpg" alt="Soccer.ru">
                            <div class="caption">
                                <p>
                                    <label class="btn btn-primary">
                                        <input name="source" value = 'soccer' type="radio"> Soccer.ru
                                    </label>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6 hero-feature">
                        <div class="thumbnail">
                            <img src="<?=$dir?>../img/bob_soccer.png" alt="bobsoccer.ru">
                            <div class="caption">
                                <p>
                                    <label class="btn btn-primary">
                                        <input name="source" value = 'bobsoccer' type="radio"> Bobsoccer.ru
                                    </label>
                                </p>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- /btn-group -->
                
                <hr/>
                <p>
                    Выберете дату<br/>
                    <small>*Ограничение на 5 дней в прошлое. Формат даты (02-04-2017) </small>
                </p>
                <input  class="tcal" name = "date" type="date" size="20" id="date" onclick="Send()">
                <button type="submit">Смотреть</button>
                <p>
                    Или просто нажмите эту кнопку<br>
                    <button name="date" value="today" id="date" type="submit">Сегодня&nbsp;</button>
                    <button name="date" value="yesterday" id="date" type="submit">Вчера</button>
                </p>

            </form>

        </div>
        <!-- /.row -->

    </div>
    <!-- /.container -->
    <hr/>
    <!-- Footer -->
    <footer>
        <div class="row text-center">
            <div class="col-lg-12">
                <p>Парсер спортивных сайтов <?=strip_tags($_COOKIE['version'])?> Copyright &copy; N-sight 2017</p>
            </div>
        </div>
    </footer>

    <!-- jQuery -->
    <script src="../js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../js/bootstrap.min.js"></script>

    <!-- Simple Calendar -->
    <script src="../js/tcal_ru.js"></script>


</body>

</html>
