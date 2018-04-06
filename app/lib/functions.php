<?php
/**
 * IDE: PhpStorm
 * Project: esser
 * Filename: functions.php
 * User: Gheorghita Mutu
 * Email: gheorghitamutu@gmail.com
 * Date: 4/6/2018
 * Time: 2:44 PM
 * @param $name
 * @param string $default
 * @return string
 */

function getParams($name, $default = '')
{
    return isset($_REQUEST[$name]) ? $_REQUEST[$name] : $default;
}