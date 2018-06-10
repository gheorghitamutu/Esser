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
  protected $params = ["apptitle" => APP_TITLE];
  
    public function __construct($uri)
    {
        switch($uri)
        {
          case '/':
          {
              $this->index($this->params);
              break;
          }
          case 'home':
          {
              $this->index($this->params);
              break;
          }
          case 'home/index':
          {
              $this->index($this->params);
              break;
          }
          default:
          {
              $this->index($this->params);
              break;
          }
        }
    }

    private function index($params)
    {
      View::CreateView('home' . DIRECTORY_SEPARATOR . 'index', $params, $params["apptitle"]);
    }
}