<?php
/**
 * Created by PhpStorm.
 * User: ghita
 * Date: 6/13/2018
 * Time: 10:04 AM
 */

class InternalServerErrorController extends Controller
{
    public function __construct()
    {
        $this->index();
    }

    public function index()
    {
        View::CreateView(
            '500',
            [],
            'Server Error!');
    }
}
