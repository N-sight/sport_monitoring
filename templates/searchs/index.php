<?

/* @var $request array[]*/
$title = "Сделайте запрос. ".title_const;
//var_dump($request);
?>

<div class="page-header">
    <h3>Чего изволите?</h3>
</div>

<form action="" method="post" enctype="multipart/form-data">
    <input type="hidden" VALUE="add" name="__action">

    <div class="row">
        <table class="table">
            <tr>
                <td><i class="fa fa-bookmark"></i> Ключевое слово в комментариях :</td>
                <td>
                    <input name="phrase" type="text" class="form-control" placeholder="...Ключевое слово*">
                </td>
            </tr>
            <tr>
                <td><i class="fa fa-map-marker"></i> Источник:</td>
                <td>
                    <select  title="источиник" name="source" class="form-control">
                        <option value="sports">sports.ru</option>
                        <option value="championat">championat.com</option>
                        <option value="sovsport">sovsport.ru</option>
                        <option value="sportexpress">sport-express.ru</option>
                        <option value="eurosport">eurosport.ru</option>
                        <option value="soccer">soccer.ru</option>
                        <option value="bobsoccer">bobsoccer.ru</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><i class="fa fa-clock-o"></i> Дата начала:</td> 
                <td>
                    <input title="Старт выборки" class="tcal" name = "dateStart" value = "<?=date('Y-m-d',time()-86400*3)?>" type="date" size="20" id="date" onclick="Send()">
                </td>
            </tr>
            <tr>
                <td><i class="fa fa-clock-o"></i> Дата финиша :</td>
                <td>
                    <input title="Конец выборки" class="tcal" name = "dateEnd" value = "<?=date('Y-m-d',time()-86400*2)?>" type="date" size="20" id="date" onclick="Send()">
                </td>
            </tr>
        </table>
    </div>
    <div class="row">
        <button type="submit" class="btn btn-success" name="submitSearch">Найти</button>
        <a class="btn btn-default" href="/home/start">Вернуться назад</a>
        <br><br>
        <h5 class="red">* Поиск работает в альфа версии.(в поиск попадает запрос вида like %запрос% поэтому постарайтесь задавать одно осмысленное слово. Дата старта сбора с 26-08-17</h5>
    </div>
</form>


