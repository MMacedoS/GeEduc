<?php

namespace App\Controllers\v1\Plan;

use App\Controllers\Controller;
use App\Interfaces\Plan\IPlanoRepository;
use App\Request\Request;
use App\Utils\Paginator;
use App\Utils\Validator;

class PlanoController extends Controller 
{
    protected $planoRepository;

    public function __construct(
        IPlanoRepository $planoRepository
    )
    {
        parent::__construct();   
        $this->planoRepository = $planoRepository; 
    }

    public function index(Request $request) 
    {
        $planos = $this->planoRepository->allPlans();
        $perPage = 10;
        $currentPage = $request->getParam('page') ? (int)$request->getParam('page') : 1;
        $paginator = new Paginator($planos, $perPage, $currentPage);
        $paginatedBoards = $paginator->getPaginatedItems();

        $data = [
            'planos' => $paginatedBoards,
            'links' => $paginator->links()
        ];
        return $this->router->view('plan/index', ['active' => 'register', 'data' => $data]); 
    }

    public function create(Request $request)
    {
        return $this->router->view('plan/create', ['active' => 'register']);
    }

    public function store(Request $request)
    {
        $data = $request->getBodyParams();

        $validator = new Validator($data);

        $rules = [
            'name' => 'required|min:1|max:100',           
            'description' => 'required',
            'amount' => 'required',
            'active' => 'required'
        ];

        if (!$validator->validate($rules)) {
            return $this->router->view(
                'plan/create', 
                [
                    'active' => 'register', 
                    'errors' => $validator->getErrors()
                ]
            );
        } 
        
        $created = $this->planoRepository->create($data);

        if(is_null($created)) {            
        return $this->router->view('plan/create', ['active' => 'register', 'danger' => true]);
        }

        return $this->router->redirect('planos/');
    }

    public function edit(Request $request, string $id)
    {
        $plan = $this->planoRepository->findByUuid($id);

        if (is_null($plan)) {
            return $this->router->view('plan/', ['active' => 'register', 'danger' => true]);
        }
        
        return $this->router->view('plan/edit', ['active' => 'register', 'plano' => $plan]);
    }

    public function update(Request $request, string $id)
    {
        $data = $request->getBodyParams();

        $plan = $this->planoRepository->findByUuid($id);

        if (is_null($plan)) {
            return $this->router->view('plan/', ['active' => 'register', 'danger' => true]);
        }

        $validator = new Validator($data);

        $rules = [
            'name' => 'required|min:1|max:100',           
            'description' => 'required',
            'amount' => 'required'
        ];

        if (!$validator->validate($rules)) {
            return $this->router->view(
                'plan/edit', 
                [
                    'active' => 'register', 
                    'errors' => $validator->getErrors()
                ]
            );
        }
        
        $updated = $this->planoRepository->update($data, $plan->id);

        if(is_null($updated)) {            
            return $this->router->view('plan/edit', ['active' => 'register', 'danger' => true]);
        }

        return $this->router->redirect('planos/');
    }

    public function destroy(Request $request, string $id)
    {
        $plan = $this->planoRepository->findByUuid($id);

        if (is_null($plan)) {
            return $this->router->view('plan/', ['active' => 'register', 'danger' => true]);
        }

        $this->planoRepository->delete($plan->id);

        return $this->router->redirect('planos/');
    }
}