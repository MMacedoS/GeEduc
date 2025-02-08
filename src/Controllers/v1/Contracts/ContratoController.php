<?php
namespace App\Controllers\v1\Contracts;

use App\Controllers\Controller;
use App\Interfaces\Student\IEstudanteMensalidadeRepository;
use App\Services\AutentiqueService;
use App\Controllers\v1\Traits\ManipulationPDF;

class ContratoController extends Controller {
  use ManipulationPDF;
  protected $autentiqueService;
  protected $estudanteMensalidadeRepository;

  public function __construct(
    IEstudanteMensalidadeRepository $estudanteMensalidadeRepository
  )
  {
    parent::__construct();

    $this->estudanteMensalidadeRepository = $estudanteMensalidadeRepository;
    $this->autentiqueService = new AutentiqueService();
  }

  public function index() {
    $students = $this->estudanteMensalidadeRepository->allMonthlyfees();
    // dd($students);
    $this->generateAndSendContracts($students);
    // dd("chega");
    return $this->router->view("contracts/index");
  }

  public function generateAndSendContracts($students) {
    foreach($students as $student) {
      $pathPDF = $this->generatePDF($student, "/Resources/Views/contracts/contrato.php", "/Resources/Views/contracts/contrato");
      if($pathPDF) {
        $this->autentiqueService->sendContract($student, $pathPDF);
      }
    }
  }
}