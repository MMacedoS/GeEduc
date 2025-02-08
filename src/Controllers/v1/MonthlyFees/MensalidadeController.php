<?php

namespace App\Controllers\v1\MonthlyFees;

use App\Controllers\Controller;
use App\Interfaces\MonthlyFees\IMensalidadeRepository;
use App\Interfaces\Plan\IPlanoRepository;
use App\Interfaces\Student\IEstudanteMensalidadeRepository;
use App\Request\Request;
use App\Utils\Paginator;
use App\Utils\Validator;

class MensalidadeController extends Controller
{
    protected $mensalidadeRepository;
    protected $estudanteMensalidadeRepository;
    protected $planosRepository;

    public function __construct(
        IMensalidadeRepository $mensalidadeRepository,
        IEstudanteMensalidadeRepository $estudanteMensalidadeRepository,
        IPlanoRepository $planosRepository
    ){
        parent::__construct();
        $this->mensalidadeRepository = $mensalidadeRepository;
        $this->estudanteMensalidadeRepository = $estudanteMensalidadeRepository;
        $this->planosRepository = $planosRepository;
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