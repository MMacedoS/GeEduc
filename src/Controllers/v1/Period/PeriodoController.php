<?php

namespace App\Controllers\v1\Period;

use App\Controllers\Controller;
use App\Interfaces\Period\IPeriodoRepository;
use App\Request\Request;
use App\Utils\Paginator;
use App\Utils\Validator; 

class PeriodoController extends Controller{
    protected $periodoRepository;

    public function __construct(
        IPeriodoRepository $periodoRepository
    ) 
    {
        parent::__construct();
        $this->periodoRepository = $periodoRepository;   
    }

    public function index(Request $request){
        if(!hasPermission('visualizar periodos')){
            return $this->router->redirect('dashboard?error=442');
        }

        $bimestres = $this->periodoRepository->all();
        $perPage = 10;
        $currentPage = $request->getParam('page') ? (int)$request->getParam('page') : 1;
        $paginator = new Paginator($bimestres, $perPage, $currentPage);
        $paginatedBoards = $paginator->getPaginatedItems();

        return $this->router->view('period/index', 
        [
            'active' => 'pedagogico', 
            'periodos' => $paginatedBoards,
            'links' => $paginator->links()
        ]);
    }

    public function create(){
        if(!hasPermission('visualizar periodos')){
            return $this->router->redirect('periodos?error=442');
        }

        return $this->router->view('period/create', ['active' => 'register']);
    }

    public function store(Request $request){
        $data = $request->getBodyParams();

        $validator = new Validator($data);

        $rules = [
            'period' => 'required|min:1|max:6'
        ];

        if(!$validator->validate($rules)){
            return $this->router->view(
                'period/create',
                [
                    'active' => 'pedagogico',
                    'errors' => $validator->getErrors()
                ]
            );
        }

        $created = $this->periodoRepository->create($data);

        if(is_null($created)){
            return $this->router->view('period/create', ['active' => 'pedagogico', 'danger' => true]);
        }

        return $this->router->redirect('periodos/');
    }

    public function edit(Request $request, $id){
        if(!hasPermission('editar periodos')){
            return $this->router->redirect('periodos?error=422');
        }

        $bimestre = $this->periodoRepository->findByUuid($id);
        
        if(is_null($bimestre)){
            return $this->router->view('period/', ['active' => 'pedagogico', 'danger' => true]);
        }

        return $this->router->view('period/edit', ['active' => 'pedagogico', 'periodo' => $bimestre]);
    }

    public function update(Request $request, $id){
        $bimestre = $this->periodoRepository->findByUuid($id);

        if(is_null($bimestre)){
            return $this->router->view('period/', ['active' => 'pedagogico', 'danger' => true]);
        }

        $data = $request->getBodyParams();

        $validator = new Validator($data);

        $rules = [
            'period' => 'required|min:1|max:6'
        ];

        if(!$validator->validate($rules)){
            return $this->router->view(
                'period/edit',
                [
                    'active' => 'pedagogico',
                    'errors' => $validator->getErrors()
                ]
            );
        }

        $updated = $this->periodoRepository->update($data, $bimestre->id);
        
        if(is_null($updated)){
            return $this->router->view('period/edit', ['active' => 'pedagogico', 'danger' => true]);
        }

        return $this->router->redirect('periodos/');
    }
}