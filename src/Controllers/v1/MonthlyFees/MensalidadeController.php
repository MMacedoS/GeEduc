<?php

namespace App\Controllers\v1\MonthlyFees;

use App\Controllers\Controller;
use App\Repositories\Bank_account\ContaBancariaRepository;
use App\Repositories\MonthlyFees\MensalidadeRepository;
use App\Repositories\Plan\PlanoRepository;
use App\Repositories\Student\EstudanteMensalidadeRepository;
use App\Request\Request;
use App\Services\BoletoBBService;
use App\Utils\BoletoTrait;
use App\Utils\LoggerHelper;
use App\Utils\Paginator;
use App\Utils\Validator;

class MensalidadeController extends Controller
{
    use BoletoTrait;

    protected $mensalidadeRepository;
    protected $estudanteMensalidadeRepository;
    protected $planosRepository;
    protected $contaBancariaRepository;

    public function __construct(){
        parent::__construct();
        $this->mensalidadeRepository = new MensalidadeRepository();
        $this->estudanteMensalidadeRepository = new EstudanteMensalidadeRepository();
        $this->planosRepository = new PlanoRepository();
        $this->contaBancariaRepository = new ContaBancariaRepository();
    }

    public function index(Request $request){
        if(!hasPermission('visualizar mensalidades')){
            return $this->router->redirect('dashboard?error=442');
        }

        $mensalidades = $this->mensalidadeRepository->allMonthlyfees();

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
                'links' => $paginator->links()
            ]
        );
    }

    public function create(Request $request){
        if(!hasPermission('visualizar mensalidades')){
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
        if(!hasPermission('editar mensalidade')){
            return $this->router->redirect('mensalidades?error=442');
        }
        
        $mensalidade = $this->mensalidadeRepository->allMonthlyfees(['uuid' => $id]);

        $banco = $this->contaBancariaRepository->findById(1);

        if(is_null($mensalidade)) {
            return $this->router->redirect("mensalidades?error");
        }
        
        $boletoservice = new BoletoBBService();


        $dados = $this->prepareTicketData($mensalidade[0], $banco);

        $boleto = $boletoservice->emitirBoleto($dados);

        LoggerHelper::logInfo(json_encode($boleto));

        dd(json_encode($boleto));

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
           
        $this->mensalidadeRepository->update($data, $mensalidade->id);    

        return $this->router->redirect("mensalidades?success");
    }

    public function destroy(Request $request, string $id) 
    {
        $mensalidade = $this->mensalidadeRepository->findByUuid($id);

        if(is_null($mensalidade)) {
            return $this->router->redirect("mensalidades?error");
        }

        $this->mensalidadeRepository->delete($mensalidade->id);

        return $this->router->redirect("mensalidades?success");
    }
}