<?php

Class ArrayHelper
{
    /**
     * Функция в качестве ключей многомерного массива выставит значения из колонки index.
     * @param $array Array массив массивов или массив объектов
     * @param $index int|string
     * @return $array
     */
    public static function index($array, $index)
    {
        $result = array();

        foreach($array as $a)
        {
            if (is_array($a))
            {
                if (!isset($a[$index])) continue; // такого индекса нет, выбрасываем из итогового массива
                $result[$a[$index]] = $a;
            }
            else
            {
                if (is_object($a))
                {
                    if ($a->$index === NULL)
                    {
                        continue;
                    } // такого индекса нет, выбрасываем из итогового массива
                    $result[$a->$index] = $a;
                }
            }
        }

        return $result;
    }
}