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

    public function __construct($view_file, $view_data)
    {
        $this->view_file = $view_file;
        $this->view_data = $view_data;
    }

    public function render()
    {
        if(file_exists(VIEW . $this->view_file . '.phtml'))
        {
            include VIEW . $this->view_file . '.phtml';
        }
    }
}