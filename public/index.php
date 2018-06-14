<?php

/**
 * IDE: PhpStorm
 * Project: esser
 * Filename: index.php
 * User: Gheorghita Mutu
 * Email: gheorghitamutu@gmail.com
 * Date: 4/5/2018
 * Time: 12:50 PM
 */

session_start();

define('ROOT', dirname(__DIR__) . DIRECTORY_SEPARATOR);

require_once ('../app/config/config.php');

require_once(APP_INIT . "load_dependencies.php");

new IndexController();
