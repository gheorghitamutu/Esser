<?php
/**
 * IDE: PhpStorm
 * Project: esser
 * Filename: Route.php
 * User: Gheorghita Mutu
 * Email: gheorghitamutu@gmail.com
 * Date: 4/6/2018
 * Time: 3:04 PM
 */

class Route
{
    public static $_uri = array();
    public static $_method = array();

    public static function add($uri, $method = null)
    {
        $uri = ($uri != '/') ? trim($uri, '/') : '/';
        Route::$_uri[] = $uri;

        if ($method != null)
        {
            Route::$_method[] =  $method;
        }
    }

    public static function submit()
    {
        $uriGetParam = isset($_GET['uri']) ? $_GET['uri'] : '/';
        // just make a redirect here for localhost blank
        foreach (Route::$_uri as $key => $value)
        {
            // echo $key .  ' ' . $value . ' ' . Route::$_method[$key] .'<br>';
            // echo ucfirst($uriGetParam) . 'Controller' . '<br>';
            if(ucfirst(explode('/', $uriGetParam)[0]) . 'Controller' == Route::$_method[$key])
            {
                $useMethod = Route::$_method[$key];
                new $useMethod($uriGetParam);
                break;
            }
        }

    }
}