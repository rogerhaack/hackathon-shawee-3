<?php

namespace App\Helpers;

class FilterString
{

    static function filter($str)
    {
        $from = "áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ";
        $to = "ÁÀÃÂÉÊÍÓÔÕÚÜÇÁÀÃÂÉÊÍÓÔÕÚÜÇ";

        $keys = array();
        $values = array();
        preg_match_all('/./u', $from, $keys);
        preg_match_all('/./u', $to, $values);
        $mapping = array_combine($keys[0], $values[0]);
        $string = strtr(trim($str), $mapping);

        return strtoupper($string);
    }

}