<?php
namespace App\Controllers\v1\Contracts;

use App\Controllers\Controller;
use App\Services\AutentiqueService;

class ContratoController extends Controller {
  protected $autentiqueService;
  public function __construct() {
    parent::__construct();
    $this->autentiqueService = new AutentiqueService();
  }

  public function index() {
    $this->autentiqueService->listarEstudantesMensalidades();
    dd("da");
    return $this->router->view("contracts/contrato");
  }
}