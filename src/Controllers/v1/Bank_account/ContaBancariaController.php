<?php

namespace App\Controllers\v1\Bank_account;

use App\Controllers\Controller;
use App\Interfaces\Bank_account\IContaBancariaRepository;
use App\Request\Request;
use App\Utils\Paginator;
use App\Utils\Validator;

class ContaBancariaController extends Controller 
{
    protected $contaBancariaRepository;

    public function __construct(
        IContaBancariaRepository $contaBancariaRepository
    )
    {
        parent::__construct();   
        $this->contaBancariaRepository = $contaBancariaRepository;
    }

    public function index(Request $request) 
    {
        if(!hasPermission('visualizar contas bancarias')) {
            return $this->router->redirect('dashboard?error=422');
        }

        $classRooms = $this->contaBancariaRepository->allBanks();
        $perPage = 10;
        $currentPage = $request->getParam('page') ? (int)$request->getParam('page') : 1;
        $paginator = new Paginator($classRooms, $perPage, $currentPage);
        $paginatedBoards = $paginator->getPaginatedItems();

        $data = [
            'contas' => $paginatedBoards,
            'links' => $paginator->links()
        ];
        return $this->router->view('bank-account/index', ['active' => 'register', 'data' => $data]); 
    }

    public function create(Request $request)
    {
        if(!hasPermission('cadastrar contas')) {
            return $this->router->redirect('bancos?error=422');
        }

        return $this->router->view('bank-account/create', ['active' => 'register']);
    }

    public function store(Request $request)
    {
        $data = $request->getBodyParams();

        $validator = new Validator($data);

        $rules = [
            'agreement' => 'required|min:1|max:45',
            'branch' => 'required|min:1|max:10',
            'account' => 'required|min:1|max:20',
            'bank' => 'required|min:1|max:100',
            'bank_code' => 'required|min:1|max:45'
        ];

        if (!$validator->validate($rules)) {
            return $this->router->view(
                'bank-account/create', 
                [
                    'active' => 'register', 
                    'errors' => $validator->getErrors()
                ]
            );
        } 
        
        $created = $this->contaBancariaRepository->create($data);

        if(is_null($created)) {            
        return $this->router->view('bank-account/create', ['active' => 'register', 'danger' => true]);
        }

        return $this->router->redirect('bancos/');
    }

    public function edit(Request $request, string $id)
    {
        if(!hasPermission('editar contas')) {
            return $this->router->redirect('bancos?error=422');
        }

        $conta = $this->contaBancariaRepository->findByUuid($id);

        if (is_null($conta)) {
            return $this->router->view('bank-account/', ['active' => 'register', 'danger' => true]);
        }
        
        return $this->router->view('bank-account/edit', ['active' => 'register', 'conta' => $conta]);
    }

    public function update(Request $request, string $id)
    {
        $data = $request->getBodyParams();

        $turma = $this->contaBancariaRepository->findByUuid($id);

        if (is_null($turma)) {
            return $this->router->view('bank-account/', ['active' => 'register', 'danger' => true]);
        }

        $validator = new Validator($data);

        $rules = [
            'agreement' => 'required|min:1|max:45',
            'branch' => 'required|min:1|max:10',
            'account' => 'required|min:1|max:20',
            'bank' => 'required|min:1|max:100',
            'bank_code' => 'required|min:1|max:45'
        ];

        if (!$validator->validate($rules)) {
            return $this->router->view(
                'bank-account/edit', 
                [
                    'active' => 'register', 
                    'errors' => $validator->getErrors()
                ]
            );
        }
        
        $updated = $this->contaBancariaRepository->update($data, $turma->id);

        if(is_null($updated)) {            
            return $this->router->view('bank-account/edit', ['active' => 'register', 'danger' => true]);
        }

        return $this->router->redirect('bancos/');
    }

    public function destroy(Request $request, string $id)
    {
        if(!hasPermission('deletar contas')) {
            return $this->router->redirect('bancos?error=422');
        }

        $turma = $this->contaBancariaRepository->findByUuid($id);

        if (is_null($turma)) {
            return $this->router->view('bank-account/', ['active' => 'register', 'danger' => true]);
        }

        $this->contaBancariaRepository->delete($turma->id);

        return $this->router->redirect('bancos/');
    }
}