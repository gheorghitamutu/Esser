<?php
/**
 * IDE: PhpStorm
 * Project: esser
 * Filename: Controller.php
 * User: Gheorghita Mutu
 * Email: gheorghitamutu@gmail.com
 * Date: 4/5/2018
 * Time: 2:08 PM
 */

class Controller extends Database
{
    public static function redirect($url){
        header("location: {$url}");
    }
}