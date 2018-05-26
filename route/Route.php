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

        if($uriGetParam == '/')
        {
            Controller::redirect('/home');
            return;
        }


        foreach (Route::$_uri as $key => $value)
        {
            if((ucfirst(explode('/', $uriGetParam)[0]) . 'Controller') == ucfirst(Route::$_method[$key]))
            {
                $useMethod = Route::$_method[$key];
                new $useMethod($uriGetParam);
                return;
            }
        }

        // if there are not routes for the url then page is not on the site
        new PageNotFoundController();
    }
}