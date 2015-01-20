<?php

class Model extends Db
{

    public function randomString($lenght = 128)
    {
        $characterList = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $num = 0;
        $string = "";
        while ($num < $lenght) {
            $string .= $characterList{mt_rand(0, (strlen($characterList) - 1))};
            $num++;
        }
        return $string;
    }

}
