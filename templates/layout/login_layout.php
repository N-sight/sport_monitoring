<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Система мониторинга футбольных СМИ.</title>


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

    <div class="container">
        <?
            $success = System::get_message('success');
            $error = System::get_message('error');
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

        ?>
        <div class="row">
            <?=$content;?>
        </div>
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
