<?php
/**
 * Created by PhpStorm.
 * User: Fr33LaNc3
 * Date: 18-Jun-18
 * Time: 09:43
 */

class ItemGroupsController extends AdmincpController
{
    private $uri;

    public function __construct($uri)
    {
        switch ($uri) {
            case 'admincp/itemgroups':
                $this->itemgroupsmanager();
                break;
        }
        $this->uri = $uri;
    }

    protected function itemgroupsmanager()
    {
        View::CreateView(
            'admincp' . DIRECTORY_SEPARATOR . 'item_groups' . DIRECTORY_SEPARATOR . 'manager',
            [
                'grouplist' => []
            ],

            APP_TITLE);
    }

}