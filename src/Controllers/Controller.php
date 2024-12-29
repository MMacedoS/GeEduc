<?php

namespace App\Controllers;

use App\Config\Router;
use App\Repositories\Balance\CaixaRepository;

class Controller {
  
    public $router;

    public function __construct() 
    {
        $this->router = new Router();
        $this->refreshBalance();
    }

    public function refreshBalance()
    {
        if(isset($_SESSION['balance'])) {
            $caixaRepository = new CaixaRepository();
            $balance = $caixaRepository->findById((int)$_SESSION['balance']->id);
            $_SESSION['balance'] = $balance;
        }
    }

    public function checkBalanceOpen() 
    {      
        if(!isset($_SESSION['balance'])) {
            http_response_code(422); 
            echo json_encode(['error' => 'caixa não foi aberto.']);
            return;
        }
    }
}