<?php

namespace App\Controllers;

use App\Config\Router;
use App\Repositories\Balance\CaixaRepository;

class Controller {
  
    public $router;

    public function __construct() 
    {
        $this->router = new Router();
    }
}