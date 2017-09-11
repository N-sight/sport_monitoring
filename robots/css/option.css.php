<?
header("Content-type: text/css; charset: UTF-8");
$dir = 'http://'.$_SERVER['HTTP_HOST']
        .substr(dirname($_SERVER['PHP_SELF']),0,-3);

?>

/* LOGO */
.logo
{
    background-image: url('<?=$dir?>img/spartak.png');
    background-repeat: no-repeat;
    background-position: center;
    background-size: auto;
}

/* SPOILER */
.spoiler_wrap input[type='button'] {
    color: #577F94;
    background-color: #D9EDF7;
    padding: 2px;
    border: 1px solid rgba(0, 0, 0, 0.1);
    border-radius: 2px;
    /*text-shadow: 1px 1px 1px rgba(0, 0, 0, 0.3);*/
    cursor: pointer;
}

.spoiler_wrap .spoiler_content {
    display: none;
    margin-top: 5px;
    padding-left: 5px;
    border-left: 3px solid #D9EDF7;
}
