<?php
/**
 * Created by PhpStorm.
 * User: tthrockmorton
 * Date: 7/19/2016
 * Time: 3:37 PM
 */

namespace app\Libraries;


use Symfony\Component\Config\Definition\Exception\Exception;

class RandomObjectGeneration
{
    /**
     * random_str
     * Generates a random string.
     *
     * @param   int         $length         Length of string to be returned
     * @param   string      $keyspace       Allowed characters to be used in string
     * @return  string
     */
    public static function random_str($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
    {
        if(is_null($length) || !is_numeric($length)) {
            throw new Exception();
        }
        $str = '';
        $max = mb_strlen($keyspace) - 1;
        for ($i = 0; $i < $length; ++$i) {
            $str .= $keyspace[random_int(0, $max)];
        }
        return $str;
    }
}