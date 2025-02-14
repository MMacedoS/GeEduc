<?php
namespace App\Controllers\v1\Contracts;

use App\Controllers\Controller;
use App\Interfaces\Student\IEstudanteMensalidadeRepository;
use App\Repositories\Contracts\ContratoRepository;
use App\Request\Request;
use App\Services\AutentiqueService;
use App\Controllers\v1\Traits\ManipulationPDF;
use App\Interfaces\Contracts\IContratoRepository;
use App\Utils\LoggerHelper;

class ContratoController extends Controller {

  use ManipulationPDF;
  protected $autentiqueService;
  protected $estudanteMensalidadeRepository;
  protected $contratoRepository;

  public function __construct(
    IEstudanteMensalidadeRepository $estudanteMensalidadeRepository,
    IContratoRepository $contratoRepository
  ) {
    parent::__construct();

    $this->estudanteMensalidadeRepository = $estudanteMensalidadeRepository;
    $this->contratoRepository = $contratoRepository;
    $this->autentiqueService = new AutentiqueService();
  }

  public function index() {
    $students = $this->estudanteMensalidadeRepository->allMonthlyfees(['verify_contract' => false]);

    return $this->router->view("contracts/index", ["estudantes" => $students]);
  }

  public function generateAndSendContracts($students) {
    $students = $this->estudanteMensalidadeRepository->allMonthlyfees(['verify_contract' => true]);
    foreach($students as $student) {
      $pathPDF = $this->generatePDF($student, "/Resources/Views/contracts/contrato.php", "/../Public/files/contracts/contrato");
      if($pathPDF) {
        $contract = $this->autentiqueService->sendContract($student, $pathPDF);
        $idContract = getJsonToObject($contract)->data->createDocument->id;
        if($idContract) {
          $data = [
            "student_id" => $student->estudante_id,
            "school_year" => Date("Y"),
            "document_id" => $idContract,
            'content' => $contract
          ];

          $this->contratoRepository->create($data);
        }
      }
    }
    // $this->deletePDF("/../Public/files/contracts/contrato.pdf");
    return $this->router->redirect("contratos");
  }

  public function webhookAutentique(Request $request) {
    $payload = $request->getJsonBody();
    $headers = $request->getHeaders();
    $event = $payload['event'];

    $verify = $this->autentiqueService->verifySignature($headers, json_encode($payload), SECRET_AUTENTIQUE);

    $verify_2 = $this->autentiqueService->verifySignature($headers, json_encode($payload), SECRET_AUTENTIQUE_CREATE_DOC);
    
    if($verify || $verify_2) {
      switch ($event['type']) {
        case 'signature.accepted':
          $this->contratoRepository->updateSignature($event["data"]["document"], $event);
          break;
        case 'signature.deleted':
            $this->contratoRepository->updateSignature($event["data"]["document"], $event);
          break;
        case 'signature.rejected':
            $this->contratoRepository->updateSignature($event["data"]["document"], $event);
          break;
        default:
          LoggerHelper::logError("Evento não identificado");
      }
      return;
    }

    LoggerHelper::logError("Erro: Assinatura não reconhecida!");
  }
}