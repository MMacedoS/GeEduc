<?php
namespace App\Controllers\v1\Contracts;

use App\Controllers\Controller;
use App\Interfaces\Student\IEstudanteMensalidadeRepository;
use App\Repositories\Contracts\ContratoRepository;
use App\Services\AutentiqueService;
use App\Controllers\v1\Traits\ManipulationPDF;

class ContratoController extends Controller {
  use ManipulationPDF;
  protected $autentiqueService;
  protected $estudanteMensalidadeRepository;
  protected $contratoRepository;

  public function __construct(
    IEstudanteMensalidadeRepository $estudanteMensalidadeRepository,
  )
  {
    parent::__construct();

    $this->estudanteMensalidadeRepository = $estudanteMensalidadeRepository;
    $this->contratoRepository = new ContratoRepository();
    $this->autentiqueService = new AutentiqueService();
  }

  public function index() {
    $students = $this->estudanteMensalidadeRepository->allMonthlyfees(['verify_contract' => false]);

    return $this->router->view("contracts/index", ["estudantes" => $students]);
  }

  public function generateAndSendContracts($students) {
    $students = $this->estudanteMensalidadeRepository->allMonthlyfees(['verify_contract' => true]);
    foreach($students as $student) {
      $pathPDF = $this->generatePDF($student, "/Resources/Views/contracts/contrato.php", "/Resources/Views/contracts/contrato");
      if($pathPDF) {
        $idContract = $this->autentiqueService->sendContract($student, $pathPDF);
        if($idContract) {
          $data = [
            "student_id" => $student->estudante_id,
            "school_year" => Date("Y"),
            "contract_url" => "https://painel.autentique.com.br/documentos/$idContract",
          ];

          $created = $this->contratoRepository->create($data);
        }
      }
    }

    return $this->router->redirect("contratos");
  }
}