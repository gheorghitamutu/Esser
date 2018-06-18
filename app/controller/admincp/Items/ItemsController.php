<?php
/**
 * Created by PhpStorm.
 * User: Fr33LaNc3
 * Date: 18-Jun-18
 * Time: 09:43
 */

class ItemsController extends AdmincpController
{
    private $uri;

    public function __construct($uri)
    {
        switch ($uri) {
            case 'admincp/itemeditor':
                $this->itemeditor();
                break;
            case 'admincp/itemmanager':
                $this->itemmanager();
                break;
        }
        $this->uri = $uri;
    }

    protected function itemeditor()
    {
        View::CreateView(
            'admincp' . DIRECTORY_SEPARATOR . 'items' . DIRECTORY_SEPARATOR . 'editor',
            [
                'grouplist' => []
            ],

            APP_TITLE);
    }

    protected function itemmanager()
    {
        View::CreateView(
            'admincp' . DIRECTORY_SEPARATOR . 'items' . DIRECTORY_SEPARATOR . 'manager',
            [
                'grouplist' => []
            ],

            APP_TITLE);
    }

}