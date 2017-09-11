<?php
    $handle = fopen(getcwd() .'/cron_log.txt' , 'a');
    $str = date('d-m-Y H:i').' '.$_SERVER['argv'][1]."\r\n"; //.$_SERVER['argv'][0].
    fwrite($handle, $str);
    fclose($handle);
    var_dump($_SERVER['argv']);
die;
