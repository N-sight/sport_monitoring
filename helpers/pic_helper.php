<?php

function image_upload ($id_obj,$file,$subfolder = false)
{
    if ($subfolder === false)
    {
        $subfolder = '';
    }
    else
    {
        $subfolder = $subfolder.'/';
    }

    $uploaddir = $_SERVER['DOCUMENT_ROOT'].'/images/'.$subfolder.'/';

    $path = array();


    foreach ($file['size'] as $key => $value)
    {
        if ($file['size'][$key] > 8388608 )
        {
            return false; // если один файл жирный  - то бросаем всю загрузку
        }
        else
        {
            $path[$key] = $uploaddir.$id_obj."_".$file['name'][$key]; // в массиве path лежат адреса картинок
        }
    }


    foreach ($file['name'] as $key => $value)
    {
        $filename = $file['name'][$key];
        $ext = mb_substr( $filename, mb_strrpos($filename, '.')+1, mb_strlen($filename) );
        if ($ext == $filename) // если у файла равен расширению то расширения- нет
        {
            return false; // файлы без расширения - не картинки , бросаем всю загрузку
        }

        if ( ($ext == 'jpg') || ($ext == 'JPG') || ($ext == 'png') || ($ext == 'PNG') || ($ext == 'JPEG') || ($ext == 'jpeg') || ($ext == 'gif') || ($ext == 'GIF') || ($ext == 'pcx') || ($ext == 'PCX') )
        {

            $copy = move_uploaded_file( $file['tmp_name'][$key], $path[$key]);
            if ( !$copy )
            {
                return false;
            }

        }
        else
        {
            return false; // файлы с неверным расширением тоже не грузим,бросаем загрузку с флагом void
        }


        //файлы переписаны , можно работать с EXIF
        if ( ($ext == 'jpg') || ($ext == 'JPG') || ($ext == 'JPEG') || ($ext == 'jpeg') )
        {
            $filename = (string) $path[$key];
            exif_orientation ($filename); // это будет правильнее

        }
    }
    return true; // Файлы записаны, аватарки сформированы
}

function form_pic_name ($file,$id_obj) // формирует массив названий на выходес приставкой id_obj
{
    $out = array();
    foreach ($file as $key => $value)
    {
        $out[$key] = $id_obj."_".$value;
    }
    return $out;
}

function del_file ($file){
    $path = $_SERVER['DOCUMENT_ROOT'].'/images/'.$file;
     if ( @unlink($path) ) return true;
    else return false;

}

function exif_orientation ($file_path) // поворачивает JPG файл в соответствии с EXIF информацией
{
    $orientation=0;
    $f=fopen($file_path,'r');
    $tmp=fread($f, 2);
    if ($tmp==chr(0xFF).chr(0xD8)) {
        $section_id_stop=array(0xFFD8,0xFFDB,0xFFC4,0xFFDD,0xFFC0,0xFFDA,0xFFD9);
        while (!feof($f)) {
            $tmp=unpack('n',fread($f,2));
            $section_id=$tmp[1];
            $tmp=unpack('n',fread($f,2));
            $section_length=$tmp[1];

            // Началась секция данных, заканчиваем поиск
            if (in_array($section_id, $section_id_stop)) {
                break;
            }

            // Найдена EXIF-секция
            if ($section_id==0xFFE1) {
                $exif=fread($f,($section_length-2));
                // Это действительно секция EXIF?
                if (substr($exif,0,4)=='Exif') {
                    // Определить порядок следования байт
                    switch (substr($exif,6,2)) {
                        case 'MM': {
                            $is_motorola=true;
                            break;
                        }
                        case 'II': {
                            $is_motorola=false;
                            break;
                        }
                    }
                    // Количество тегов
                    if ($is_motorola) {
                        $tmp=unpack('N',substr($exif,10,4));
                        $offset_tags=$tmp[1];
                        $tmp=unpack('n',substr($exif,14,2));
                        $num_of_tags=$tmp[1];
                    }
                    else {
                        $tmp=unpack('V',substr($exif,10,4));
                        $offset_tags=$tmp[1];
                        $tmp=unpack('v',substr($exif,14,2));
                        $num_of_tags=$tmp[1];
                    }
                    if ($num_of_tags==0) { return true; }

                    $offset=$offset_tags+8;

                    // Поискать тег Orientation
                    for ($i=0; $i<$num_of_tags; $i++) {
                        if ($is_motorola) {
                            $tmp=unpack('n',substr($exif,$offset,2));
                            $tag_id=$tmp[1];
                            $tmp=unpack('n',substr($exif,$offset+8,2));
                            $value=$tmp[1];
                        }
                        else {
                            $tmp=unpack('v',substr($exif,$offset,2));
                            $tag_id=$tmp[1];
                            $tmp=unpack('v',substr($exif,$offset+8,2));
                            $value=$tmp[1];
                        }
                        $offset+=12;

                        // Orientation
                        if ($tag_id==0x0112) {
                            $orientation=$value;
                            break;
                        }
                    }
                }
            }
            else {
                // Пропустить секцию
                fseek($f, ($section_length-2), SEEK_CUR);
            }
            // Тег Orientation найден
            if ($orientation) { break; }
        }
    }
    fclose($f);

    $image = imagecreatefromjpeg($file_path);
    if ($orientation) {
        switch($orientation) {
            // Поворот на 180 градусов
            case 3: {
                $image=imagerotate($image,180,0);
                break;
            }
            // Поворот вправо на 90 градусов
            case 6: {
                $image=imagerotate($image,-90,0);
                break;
            }
            // Поворот влево на 90 градусов
            case 8: {
                $image=imagerotate($image,90,0);
                break;
            }
        }
    }

    imagejpeg ($image,$file_path,80);
    return true;
}

function clear_tmp_pic()
{
    $path = $_SERVER['DOCUMENT_ROOT'] . '/images/tmp/';
    $entries = scandir($path);
    foreach ($entries as $entry)
    {
        if (mb_strpos($entry, System::get_user()->username) === 0)
        {
            $filename = $path.$entry;

            if (!unlink($filename))
            {
                return false;
            }
        }
    }
    return true;
}

function copy_tmp_pic($id)
{
    $path = $_SERVER['DOCUMENT_ROOT'] . '/images/tmp/';
    $dest_path = $_SERVER['DOCUMENT_ROOT'] . '/images/';
    $entries = scandir($path);

    foreach ($entries as $entry)
    {
        if (mb_strpos($entry, System::get_user()->username) === 0)
        {
            $filename = $path.$entry;

            $name = str_replace( System::get_user()->username,$id,$entry);
            $destination =$dest_path.$name;


            if (file_exists($filename))
            {
                if (!copy ($filename,$destination))
                {
                    return false;
                }
            }
            else
            {
                deb($filename);
            }
        }
    }
    return true;
}

/// DEPRECATED

function img_resize($src, $dest, $width, $height, $rgb = 0xFFFFFF, $quality = 100)
{
    if (!file_exists($src)) return false;

    $size = getimagesize($src);

    if ($size === false) return false;

    // Определяем исходный формат по MIME-информации, предоставленной
    // функцией getimagesize, и выбираем соответствующую формату
    // imagecreatefrom-функцию.
    // imagecreatefrom-функция создает новое изображение из файла или URL
    $format = strtolower(substr($size['mime'], strpos($size['mime'], '/')+1));
    $icfunc = "imagecreatefrom" . $format;
    if (!function_exists($icfunc)) return false;

    $x_ratio = $width / $size[0];
    $y_ratio = $height / $size[1];

    $ratio       = min($x_ratio, $y_ratio);
    $use_x_ratio = ($x_ratio == $ratio);

    $new_width   = $use_x_ratio  ? $width  : floor($size[0] * $ratio);
    $new_height  = !$use_x_ratio ? $height : floor($size[1] * $ratio);
    $new_left    = $use_x_ratio  ? 0 : floor(($width - $new_width) / 2);
    $new_top     = !$use_x_ratio ? 0 : floor(($height - $new_height) / 2);

    $isrc = $icfunc($src);
    $idest = imagecreatetruecolor($width, $height);
    //создает полноцветное изображение c заданными размерами

    imagefill($idest, 0, 0, $rgb);  // делаем заливку
    imagecopyresampled($idest, $isrc, $new_left, $new_top, 0, 0,
        $new_width, $new_height, $size[0], $size[1]);
    //  копирует и изменяет размеры части изображения

    imagejpeg($idest, $dest, $quality); //выводит изображение в файл (но можно и в браузер)

    imagedestroy($isrc); //освобождает память после imagecreate()
    imagedestroy($idest);

    return true;
}







