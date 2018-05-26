<?php
/**
 * IDE: PhpStorm
 * Project: esser
 * Filename: Routes.php
 * User: Gheorghita Mutu
 * Email: gheorghitamutu@gmail.com
 * Date: 4/6/2018
 * Time: 3:14 PM
 */

Route::add('/',         'HomeController');
Route::add('/home',     'HomeController');
Route::add('/login',    'LoginController');
Route::add('/register', 'RegisterController');
Route::add('/user',     'UserController');
Route::add('/admincp',  'AdmincpController');