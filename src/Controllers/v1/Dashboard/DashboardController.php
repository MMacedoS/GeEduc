<?php

namespace App\Controllers\v1\Dashboard;

use App\Controllers\Controller;
use App\Repositories\Product\ProdutoRepository;
use App\Repositories\Reservate\ReservaRepository;
use App\Request\Request;
use App\Utils\Paginator;

class DashboardController extends Controller
{
    protected $reservaRepository;
    protected $produtoRepository;

    public function __construct() {
        parent::__construct();
    }
    
    public function index(Request $request) {
        return $this->router->view('dashboard/index', ['active' => 'dashboard']);
    }

    public function indexFacility(Request $request) {


        return $this->router->view('dashboard/facility', ['active' => 'dashboard']);
    }
}