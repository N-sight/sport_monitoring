<?
/* @var $out array[]*/
/* @var $request string*/
/* @var $last_time string*/
$title = "Нашли по вашему запросу. ".title_const;
?>

<div class="page-header">
    <h3>Поисковая выдача</h3>
</div>
<?
if (count($out) == 0)
{
?>
    <p align="center" >По ключевому запросу <b>&lt;<?=$request?>&gt;</b> мы ничего не нашли</p>
    <p align="center"><img src="/img/ball.png" alt="Футбольный мяч"></p>
<?
}
else
{
    ?>
    <table class="table table-striped">
        <caption align="center">Найдено записей : <?= count($out) ?> , последнее обращение робота к источнику было <?=$last_time?></caption>
        <thead>
        <tr>
            <td>&nbsp;#&nbsp;</td>
            <td>&nbsp;Ссылка&nbsp;</td>
            <td>&nbsp;Заголовок&nbsp;</td>
            <td>&nbsp;Комментарий&nbsp;</td>
            <td>&nbsp;Время&nbsp;</td>
            <td>&nbsp;Никнейм&nbsp;</td>
        </tr>
        </thead>
        <tbody>
        <?
        for ($i = 0; $i < count($out); $i++) {
            ?>
            <tr>
                <td><?= $i+1 ?></td>
                <td><a target="_blank" href="<?= $out[$i]->url ?>"><?= $out[$i]->url ?></a></td>
                <td><?= $out[$i]->header ?></td>
                <td><?= $out[$i]->text ?></td>
                <td><?= date('d-m-Y H:i', strtotime($out[$i]->time)) ?></td>
                <td><?= $out[$i]->user ?></td>
            </tr>
            <?
        }
        ?>
        </tbody>
    </table>
<?php
}
?>
