<?
/* @var $out array[]*/
/* @var $page int*/
/* @var $pages int*/
$title = "Список запросов. ".title_const;
//var_dump($pages);
?>

<div class="page-header">
    <h3>Список запросов</h3>
</div>

<table class="table table-striped">
    <thead>
    <tr>
        <td>&nbsp;#&nbsp;</td>
        <td>&nbsp;source&nbsp;</td>
        <td>&nbsp;time&nbsp;</td>
        <td>&nbsp;load date&nbsp;</td>
        <td>&nbsp;ip&nbsp;</td>
    </tr>
    </thead>
    <tbody>
    <!-- вывод здесь -->
    <?
    for ($i=0;$i<count($out);$i++)
        {
            //if (!isset($out[$i]->id )) continue;
    ?>
        <tr>
            <td><?=$out[$i]->id?></td>
            <td><?=$out[$i]->source?></td>
            <td><?=date('d-m-Y  H:i',$out[$i]->time)?></td>
            <td><?=$out[$i]->date?></td>
            <td>
                <?
                    if ($out[$i]->ip == '94.130.57.170') echo 'Слава германия';
                    elseif ($out[$i]->ip == '192.168.34.164, 77.105.145.116') echo 'Володя';
                    elseif ($out[$i]->ip == '91.224.207.217') echo 'Володя';
                    elseif ($out[$i]->ip == '89.178.92.195') echo 'Слава Москва';
                    elseif ($out[$i]->ip == '127.0.0.1') echo 'Локальная машина';
                    elseif ($out[$i]->ip == '91.224.207.209') echo 'Володя';
                    elseif ($out[$i]->ip == '128.68.24.202') echo 'Слава Москва';
                    else echo $out[$i]->ip;
                ?>
            </td>
        </tr>
    <?
        }
    ?>
    </tbody>
</table>
<hr/>
<table class="table-pagination">
    <tr>
        <?
            $decade  =  floor(($page-1)/10); //текущая декада для отображения

            if ($decade>0){
        ?>
                <td><a href="/logfetcher/list/<?=($decade-1)*10+10?>"><b>&lt;&lt;</b></a></td>
        <?
            }

            // тут проработать момент с существованием 
            
            $start = 1+$decade*10;
            $end = ($decade+1)*10+1;
            if ($end>$pages) $end = $pages+1;

        
            for ($i=$start;
                 $i<$end;
                 $i++)
            {

                if ($i==$page)
                {
        ?>
                    <td><a style="border:3px #808080 solid;border-radius: 5px; background-color:#f9f9f9;padding: 7px" href="/logfetcher/list/<?=$i?>"><?=$i?></a></td>
        <?
                }
                else
                {
        ?>
                    <td><a href="/logfetcher/list/<?= $i ?>"><?= $i?></a></td>
        <?
                }
            }

            if (( $decade != floor($pages/10)) ) // не последняя декада
            {
        ?>
                    <td><a href="/logfetcher/list/<?= ($decade+1)*10+1?>"><b>&gt;&gt;</b></a></td>
        <?
            }
        ?>


    </tr>
</table>

