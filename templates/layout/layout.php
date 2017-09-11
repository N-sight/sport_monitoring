<?php
$title = isset($title) ? $title : title_const;
?>
<!DOCTYPE HTML>
<html lang="ru">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?=$title?></title>


    <!-- Bootstrap Core CSS -->
    <link href="/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="/bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">


    <!-- Custom CSS -->
    <link href="/dist/css/sb-admin-2.css" rel="stylesheet">

    <!-- MY CSS -->
    <link href="/dist/css/my.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="/bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- Simple-Calendar -->
    <link href="/dist/css/tcal.css" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="/js/html5shiv.js"></script>
    <script src="/js/respond.min.js"></script>
    <![endif]-->


</head>

<body>

<div id="wrapper">

    <!-- Navigation -->
    <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

            <span class="navbar-brand">&nbsp;Sport-monitor</span>

        </div>

        <ul class="nav navbar-top-links navbar-right">
            <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                    <i class="fa fa-user fa-fw"></i>  <i class="fa fa-caret-down"></i>
                </a>
                <ul class="dropdown-menu dropdown-user">
                    <li>
                        <a href="/users/article/<?= System::get_user()->id ?>><i class="fa fa-user fa-fw"></i><span class="blue"> <?=System::get_user()->username?></span> Профиль</a>
                    </li>
                    <li class="divider"></li>
                    <li><a href="/auth/logout"><i class="fa fa-sign-out fa-fw"></i> Выйти</a>
                    </li>
                </ul>
                <!-- /.dropdown-user -->
            </li>
            <!-- /.dropdown -->
        </ul>
      

        <div class="navbar-default sidebar" role="navigation">
            <div class="sidebar-nav navbar-collapse">
                <ul class="nav" id="side-menu">
                    <li>
                        <a class="btn btn-default mg10" href="/search/list"><i class="fa fa-info-circle fa-2x"></i> &nbsp;<b>Поисковый запрос</b></a>
                    </li>
                    <li>
                        <a class="btn btn-default mg10" href="/searchuser/list"><i class="fa fa-user-md fa-2x"></i> &nbsp;<b>Поиск по никнейму</b></a>
                    </li>
                   <!-- <li>

                    </li> -->
                </ul>
            </div>
            <!-- /.sidebar-collapse -->
        </div>
        <!-- /.navbar-static-side -->
    </nav>

    <!-- Page Content -->
    <div id="page-wrapper">
        <div class="container-fluid">
            <br>
            <?
                $success = System::get_message('success');
                $error = System::get_message('error');
                $warning = System::get_message('warning');
                if ($success !== NULL)
                {
                    ?>
                        <div class = "alert alert-success"><?=$success?></div>
                    <?
                }
                if ($error !== NULL)
                {
                    ?>
                        <div class = "alert alert-danger"><?=$error?></div>
                    <?
                }
                if ($warning !== NULL)
                {
                    ?>
                        <div class = "alert alert-warning"><?=$warning?></div>
                    <?
                }


            ?>

            <?=$content;?>                                <!-- вывод обертки тут-->

        </div>
        <!-- /.container-fluid -->
    </div>
    <!-- /#page-wrapper -->

</div>
<!-- /#wrapper -->

<!-- jQuery -->
<script src="/bower_components/jquery/dist/jquery.min.js"></script>

<!-- Bootstrap Core JavaScript -->
<script src="/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

<!-- Metis Menu Plugin JavaScript -->
<script src="/bower_components/metisMenu/dist/metisMenu.min.js"></script>


<!-- Custom Theme JavaScript -->
<script src="/dist/js/sb-admin-2.js"></script>

<!-- Simple Calendar -->
<script src="/js/tcal.js"></script>

<!-- FlipBox -->
<script src="/fliplightbox/fliplightbox.min.js"></script>
<script>
    $('body').flipLightBox({
        lightbox_flip_speed: 500,
        lightbox_border_color: '#666666'
    });
    
</script>
</body>

</html>

