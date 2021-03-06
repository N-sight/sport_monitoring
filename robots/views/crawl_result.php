<?php
/* @var $line array[]*/
/* @var $yesterday string*/
/* @var $res_name string*/

$dir = 'http://'.$_SERVER['HTTP_HOST']
    .dirname($_SERVER['PHP_SELF'])
    .'/';
?>
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="n_sight">

    <title>Парсер спортивных сайтов</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/heroic-features.css" rel="stylesheet">
    <!-- Another CSS -->
    <link href="css/option.css.php" rel="stylesheet">
    <link rel="stylesheet" type="text/css" media="screen" href="<?=$dir?>css/option.css.php">
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
    <!-- Page Features -->
    <div class="row text-center">
        <p class="text-center"><a href="<?=$dir?>index.php"><button>На главную</button></a></p>
        <div class = "alert alert-info">
            <h2 class="text-center">На ресурсе <?=$res_name?> за <?=$yesterday?> краулер нашел:</h2>
        </div>

        <table class="table table-bordered text-center">
            <?
                for($i=0;$i<count($line);$i++)
                {
            ?>
                    <tr>
                        <td><a target="_blank" href="<?=$line[$i]['url']?>"><?=$line[$i]['url']?></a></td><td><?=$line[$i]['header']?></td>
                    </tr>
            <?
                }
            ?>
        </table>
        <p>
            <br>
            Выдрали статей : <?=count($line)?>
            OK <?=$res_name?>, парсинг завершён.
        </p>
        <p class="text-center"><a href="#"><button>Наверх</button></a>&nbsp;<a href="<?=$dir?>index.php"><button>На главную</button></a></p>
    </div>
    <!-- /Page Features -->
</div>
<!-- /.container -->

<!-- Footer -->
<footer>
    <div class="row text-center">
        <div class="col-lg-12">
            <p>Парсер спортивных сайтов <?=strip_tags($_COOKIE['version'])?> Copyright &copy; N-sight 2017</p>
        </div>
    </div>
</footer>

<!-- jQuery -->
<script src="js/jquery.js"></script>

<!-- Bootstrap Core JavaScript -->
<script src="js/bootstrap.min.js"></script>

</body>

</