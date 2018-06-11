<?php
/**
 * IDE: PhpStorm
 * Project: esser
 * Filename: View.php
 * User: Gheorghita Mutu
 * Email: gheorghitamutu@gmail.com
 * Date: 4/5/2018
 * Time: 2:09 PM
 */

class View
{
    protected $view_file;
    protected $view_data;
    protected $page_title;

    public function setPageTitle($page_title)
    {
        $this->page_title = $page_title;
    }

    public function __construct($view_file, $view_data)
    {
        $this->view_file = $view_file;
        $this->view_data = $view_data;
        $this->page_title = "Default page title!";
    }

    public function render()
    {
        if(file_exists(VIEW . $this->view_file . '.phtml'))
        {
            include VIEW . $this->view_file . '.phtml';
        }
        else
        {
          echo 'View file doesn\'t exist: ' . VIEW . $this->view_file . '.phtml';
        }
    }

    public function getAction() {
        // checking??
        // if you want to modify stuff based on what view you are in
        return explode('\\', $this->view_file)[1];
    }

    public static function CreateView($view_file, $view_data, $page_title)
    {
        $view = new View($view_file, $view_data);
        $view->setPageTitle($page_title);
        $view->render();
    }
}