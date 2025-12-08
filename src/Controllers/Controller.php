<?php

namespace App\Controllers;

use App\Config\Router;
use App\Repositories\Balance\CaixaRepository;

class Controller
{

    public $router;
    protected $active;
    protected $routeView;
    protected $redirect;

    public function __construct()
    {
        $this->router = new Router();
    }
}
