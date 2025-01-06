<?php

namespace App\Controllers\v1\MonthlyFees;

use App\Controllers\Controller;
use App\Repositories\MonthlyFees\MensalidadeRepository;
use App\Repositories\Student\EstudanteRepository;
use App\Request\Request;
use App\Utils\Paginator;
use App\Utils\Validator;

class MensalidadeController extends Controller{
    protected $mensalidadeRepository;

    public function __construct(){
        parent::__construct();
        $this->mensalidadeRepository = new MensalidadeRepository();
    }

    public function index(Request $request){
        if(!hasPermission('visualizar mensalidades')){
            return $this->router->redirect('dashboard?error=442');
        }
    }
}