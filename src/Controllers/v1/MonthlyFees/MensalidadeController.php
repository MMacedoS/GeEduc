<?php

namespace App\Controllers\v1\MonthlyFees;

use App\Controllers\Controller;
use App\Interfaces\MonthlyFees\IMensalidadeRepository;
use App\Interfaces\Plan\IPlanoRepository;
use App\Interfaces\Student\IEstudanteMensalidadeRepository;
use App\Interfaces\Ticket\IBoletoRepository;
use App\Repositories\Bank_account\ContaBancariaRepository;
use App\Request\Request;
use App\Services\BancoBrasilWebhookHandler;
use App\Services\BoletoBBService;
use App\Utils\LoggerHelper;
use App\Utils\Paginator;
use App\Utils\Validator;
use DateTime;

class MensalidadeController extends Controller
{
    protected $mensalidadeRepository;
    protected $estudanteMensalidadeRepository;
    protected $planosRepository;
    protected $boletoRepository;

    public function __construct(
        IMensalidadeRepository $mensalidadeRepository,
        IEstudanteMensalidadeRepository $estudanteMensalidadeRepository,
        IPlanoRepository $planosRepository,
        IBoletoRepository $boletoRepository
    ){
        parent::__construct();
        $this->mensalidadeRepository = $mensalidadeRepository;
        $this->estudanteMensalidadeRepository = $estudanteMensalidadeRepository;
        $this->planosRepository = $planosRepository;
        $this->boletoRepository = $boletoRepository;
    }

    public function index(Request $request){
        if(!hasPermission('visualizar_mensalidades')){
            return $this->router->redirect('dashboard?error=442');
        }

        $params = $request->getQueryParams();

        $mensalidades = $this->mensalidadeRepository->allMonthlyfees($params);

        $perPage = 10;
        $currentPage  =$request->getParam('page') ? (int)$request->getParam('page') : 1;

        $paginator = new Paginator(
            $mensalidades, 
            $perPage, 
            $currentPage
        );

        $paginatedBoards = $paginator->getPaginatedItems();
        
        return $this->router->view('/monthly-fees/index', 
            [
                'active' => 'register', 
                'mensalidades' => $paginatedBoards, 
                'links' => $paginator->links(),
                'searchFilter' => $params['student_name'] ?? null,
                'situation'=> $params['situation'] ?? null,
                'start_date'=> $params['start_date'] ?? null,
                'end_date'=> $params['end_date'] ?? null
            ]
        );
    }

    public function create(Request $request){
        if(!hasPermission('visualizar_mensalidades')){
            return $this->router->redirect('dashboard?error=442');
        }

        $student = $this->estudanteMensalidadeRepository->allMonthlyfees(['active' => 1]);

        if (is_null($student)) {
            return $this->router->redirect("mensalidades?error=sem estudantes");
        }

        $planos = $this->planosRepository->allPlans(["active" => 1]);
        
        return $this->router->view('/monthly-fees/create', 
            [
                'active' => 'register', 
                'estudantes' => $student,
                'planos' => $planos
            ]
        );
    }
    
    public function store(Request $request) 
    {
        $data = $request->getBodyParams();

        $student = $this->estudanteMensalidadeRepository->allMonthlyfees(['active' => 1]);

        $planos = $this->planosRepository->allPlans(["active" => 1]);

        $validator = new Validator($data);

        $rules = [
            "expiration_date" => "required",
            "monthly_day" => "required",
            "students_id" => "required",
            "amount" => "required",
        ];

        if (!$validator->validate($rules)) {
            return $this->router->view('/monthly-fees/create', 
                [
                    'active' => 'register', 
                    'estudantes' => $student,
                    'planos' => $planos
                ]
            );
        }

        $planos = $this->planosRepository->planByAmmount($data['plan_amount']);

        $data['plan_id'] = is_null($planos) ? 1 : $planos->id;

        if (empty($data['students_id'])) {
            return $this->router->view('/monthly-fees/create', 
                [
                    'active' => 'register', 
                    'estudantes' => $student,
                    'planos' => $planos
                ]
            );
        }

        foreach ($data['students_id'] as $key => $value) {
           $data["studante_monthly_id"] = $value;
           $this->mensalidadeRepository->create($data);
        }    

        return $this->router->redirect("mensalidades?success");
    }

    public function edit(Request $request, string $id){
        if(!hasPermission('editar_mensalidade')){
            return $this->router->redirect('mensalidades?error=442');
        }

        $mensalidade = $this->mensalidadeRepository->findByUuid($id);

        if(is_null($mensalidade)) {
            return $this->router->redirect("mensalidades?error");
        }

        $student = $this->estudanteMensalidadeRepository->allMonthlyfees(['active' => 1]);

        if (is_null($student)) {
            return $this->router->redirect("mensalidades?error=sem estudantes");
        }

        $planos = $this->planosRepository->allPlans(["active" => 1]);
        
        return $this->router->view('/monthly-fees/edit', 
            [
                'active' => 'register', 
                'estudantes' => $student,
                'planos' => $planos,
                'mensalidade' => $mensalidade
            ]
        );
    }

    public function update(Request $request, string $id) 
    {
        $mensalidade = $this->mensalidadeRepository->findByUuid($id);

        if(is_null($mensalidade)) {
            return $this->router->redirect("mensalidades?error");
        }

        $data = $request->getBodyParams();

        $student = $this->estudanteMensalidadeRepository->allMonthlyfees(['active' => 1]);

        $planos = $this->planosRepository->allPlans(["active" => 1]);

        $validator = new Validator($data);

        $rules = [
            "expiration_date" => "required",
            "amount" => "required",
        ];

        if (!$validator->validate($rules)) {
            return $this->router->view('/monthly-fees/edit', 
                [
                    'active' => 'register', 
                    'estudantes' => $student,
                    'planos' => $planos
                ]
            );
        }

        $planos = $this->planosRepository->planByAmmount($data['plan_amount']);

        $data['plan_id'] = is_null($planos) ? 1 : $planos->id;
           
        $mensalidade = $this->mensalidadeRepository->update($data, $mensalidade->id);    
        $dataVencimento = new DateTime($mensalidade->data_vencimento);
        $alteracoes = [
            'indicadorNovaDataVencimento' => 'S',
            'alteracaoData' => [
                'novaDataVencimento' => $dataVencimento->format('d.m.Y')
            ],
            'indicadorNovoValorNominal' => 'S',
            'alteracaoValor' => [
                'novoValorNominal' => $mensalidade->valor
            ]
        ];

        if (is_null($mensalidade->nosso_numero)) {
            $serveiceBB = new BoletoBBService();
            $contaBancariaRepository = new ContaBancariaRepository();

            $convenio = $contaBancariaRepository->findById(1);

            $serveiceBB->alterarBoleto($mensalidade->nosso_numero, $convenio, $alteracoes);
        }

        return $this->router->redirect("mensalidades?success");
    }

    public function destroy(Request $request, string $id) 
    {
        $mensalidade = $this->mensalidadeRepository->findByUuid($id);

        if(is_null($mensalidade)) {
            return $this->router->redirect("mensalidades?error");
        }

        if (is_null($mensalidade->nosso_numero)) {
            $serveiceBB = new BoletoBBService();
            $contaBancariaRepository = new ContaBancariaRepository();

            $convenio = $contaBancariaRepository->findById(1);

            $serveiceBB->cancelarBoleto($mensalidade->nosso_numero, $convenio);
        }

        $this->mensalidadeRepository->delete($mensalidade->id);

        return $this->router->redirect("mensalidades?success");
    }

    public function print(Request $request, $mensalidade_uuid) 
    {
        $mensalidade = $this->mensalidadeRepository->findByUuid($mensalidade_uuid);

        if (is_null($mensalidade)) {
            return $this->router->redirect("mensalidades?error");
        }

        $boleto = $this->boletoRepository->ticketByMonthlyId((int)$mensalidade->id);
        $estudante_mensalidade = $this->estudanteMensalidadeRepository->allMonthlyfees(['student_monthly_id' => $mensalidade->estudante_mensalidade_id]);

        if (empty($estudante_mensalidade) || is_null($boleto)) {
            return $this->router->redirect("mensalidades?error");
        }

        return $this->router->view('/monthly-fees/boleto', 
            [
                'active' => 'register', 
                'boleto' => $boleto, 
                'estudante_mensalidade' => $estudante_mensalidade[0],
                'mensalidade' => $mensalidade
            ]
        );
    }

    public function webhookBB(Request $request) 
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(["error" => "Método não permitido"]);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(["error" => "Método não permitido"]);
            exit;
        }

        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!$data) {
            http_response_code(400);
            echo json_encode(["error" => "JSON inválido"]);
            exit;
        }

        $webhookHandler = new BancoBrasilWebhookHandler();
        $webhookHandler->handle($data);

        // Retorna resposta de sucesso
        http_response_code(200);
        echo json_encode(["message" => "Webhook processado com sucesso"]);
        exit;
    }
}