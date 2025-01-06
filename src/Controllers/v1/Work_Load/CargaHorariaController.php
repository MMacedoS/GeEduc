<?php

namespace App\Controllers\v1\Work_Load;

use App\Controllers\Controller;
use App\Repositories\Work_Load\CargaHorariaRepository;
use App\Request\Request;
use App\Utils\Paginator;
use App\Utils\Validator;

class CargaHorariaController extends Controller{

    protected $cargaHorariaRepository;

    public function __construct(){
        parent::__construct();
        $this->cargaHorariaRepository = new CargaHorariaRepository();
    }

    public function index(Request $request){
        $cargaHoraria = $this->cargaHorariaRepository->all();
        $perPage = 10;
        $currentPage = $request->getParam('page') ? (int)$request->getParam('page') : 1;
        $paginator = new Paginator($cargaHoraria, $perPage, $currentPage);
        $paginatedBoards = $paginator->getPaginatedItems();

        $data = [
            'carga_horaria' => $paginatedBoards,
            'links' => $paginator->links()
        ];

        return $this->router->view('/work_load/index', ['active' => 'register', 'data' => $data]);
    }

    public function create(){
        return $this->router->view('/work_load/create', ['active' => 'register']);
    }

    public function store(Request $request){
        $data = $request->getBodyParams();

        $validator = new Validator($data);

        $rules = [
            'load' => 'required|min:1|max:45',
            'active' => 'required'
        ];

        if(!$validator->validate($rules)){
            return $this->router->view('work_load/create', [
                'active' => 'register',
                'errors' => $validator->getErrors()
            ]);
        }

        $created = $this->cargaHorariaRepository->create($data);

        if(is_null($created)){
            return $this->router->view('work_load/create', ['active' => 'register', 'danger' => true]);
        }

        return $this->router->redirect('carga-horaria/');
    }

    public function edit(Request $request, $id){
        $carga_horaria = $this->cargaHorariaRepository->findByUuid($id);

        if(is_null($carga_horaria)){
            return $this->router->view('work_load/', ['active' => 'register', 'danger' => true]);
        }

        return $this->router->view('work_load/edit', ['active' => 'register', 'carga_horaria' => $carga_horaria]);
    }

    public function update(Request $request, $id){
        $data = $request->getBodyParams();

        $carga_horaria = $this->cargaHorariaRepository->findByUuid($id);

        if(is_null($carga_horaria)){
            return $this->router->view('work_load', ['active' => 'register', 'danger' => true]);
        }

        $validator = new Validator($data);

        $rules = [
            'load' => 'required|min:1|max:45'
        ];

        if(!$validator->validate($rules)){
            return $this->router->view('work_load/edit', [
                'active' => 'register',
                'errors' => $validator->getErrors()
            ]);
        }

        $updated = $this->cargaHorariaRepository->update($data, $carga_horaria->id);
        
        if(is_null($updated)){
            return $this->router->view('work_load/edit', ['active' => 'register', 'danger' => true]);
        }

        return $this->router->redirect('carga-horaria/');
    }

    public function destroy(Request $request, $id){
        $carga_horaria = $this->cargaHorariaRepository->findByUuid($id);

        if(is_null($carga_horaria)){
            return $this->router->view('work_load/', ['active' => 'register', 'danger' => true]);
        }

        $this->cargaHorariaRepository->delete($carga_horaria->id);

        return $this->router->redirect('carga-horaria/');
    }

}