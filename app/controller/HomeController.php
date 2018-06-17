<?php
/**
 * IDE: PhpStorm
 * Project: esser
 * Filename: HomeController.php
 * User: Gheorghita Mutu
 * Email: gheorghitamutu@gmail.com
 * Date: 4/5/2018
 * Time: 1:28 PM
 */

class HomeController extends Controller
{
    public function __construct($uri)
    {
        if($this->session_authenticate())
        {
            self::redirect('/user');
            return;
        }
        switch($uri)
        {
          case '/':
          {
              $this->index();
              break;
          }
          case 'home':
          {
              $this->index();
              break;
          }
            case 'home/index':
          {
              $this->index();
              break;
          }
          default:
          {
              self::redirect('/');
              break;
          }
        }
    }

    private function index()
    {
        View::CreateView(
            'home' . DIRECTORY_SEPARATOR . 'index',
            [],
            APP_TITLE);
    }
}
