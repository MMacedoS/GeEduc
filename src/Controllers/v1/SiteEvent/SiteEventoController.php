<?php

namespace App\Controllers\v1\SiteEvent;

use App\Controllers\Controller;
use App\Repositories\SiteEvent\SiteEventoRepository;
use App\Repositories\SiteArchive\SiteArquivoRepository;
use App\Request\Request;
use App\Utils\Paginator;
use App\Utils\Validator;

class SiteEventoController extends Controller {

    protected $siteEventoRepository;
    protected $siteArquivoRepository;

    public function __construct(){
        parent::__construct();
        $this->siteEventoRepository = new SiteEventoRepository();
        $this->siteArquivoRepository = new SiteArquivoRepository();
    }

    public function index(Request $request){
        $site_eventos = $this->siteEventoRepository->allSiteEvents();
        $perPage = 10;
        $currentPage = $request->getParam('page') ? (int)$request->getParam('page') : 1;
        $paginator = new Paginator($site_eventos, $perPage, $currentPage);
        $paginatedBoards = $paginator->getPaginatedItems();

        $data = [
            'site_eventos' => $paginatedBoards,
            'links' => $paginator->links()
        ];

        return $this->router->view('/site-events/index', [
            'active' => 'pedagogico',
            'data' => $data
        ]);
    }

    public function create(Request $request){
        return $this->router->view('/site-events/create', ['active' => 'pedagogico']);
    }

    public function store(Request $request){
        $data = $request->getBodyParams();

        if(isset($_FILES['arquivo'])){
            $data['arquivo'] = $_FILES['arquivo'];
        }

        $dir = $_SERVER['DOCUMENT_ROOT'] . '/Public/files/site/events/';

        $validator = new Validator($data);

        $rules = [
            'name' => 'required|min:1|max:100',
            'arquivo' => 'required',
            'description' => 'max:255'
        ];

        if(!$validator->validate($rules)){
            return $this->router->view('site-events/create', [
                'active' => 'pedagogico',
                'errors' => $validator->getErrors()
            ]);
        }
        
        $created = $this->siteEventoRepository->saveAll($data, $dir);

        if(is_null($created)){
            return $this->router->view('site-events/create', [
                'active' => 'pedagogico', 
                'danger' => true
            ]);
        }

        return $this->router->redirect('site-eventos/');
    }

    public function edit(Request $request, $id){
        $site_evento = $this->siteEventoRepository->findByUuid($id);

        if(is_null($site_evento)){
            return $this->router->view('site-events/', [
                'active' => 'register',
                'danger' => true
            ]);
        }

        return $this->router->view('site-events/edit', [
            'active' => 'register',
            'site_evento' => $site_evento
        ]);
    }

    public function update(Request $request, $id){
        $data = $request->getBodyParams();

        if(isset($_FILES['arquivo'])){
            $data['arquivo'] = $_FILES['arquivo'];
        }

        $dir = $_SERVER['DOCUMENT_ROOT'] . '/Public/files/site/events/';

        $site_evento = $this->siteEventoRepository->findByUuid($id);

        if(is_null($site_evento)){
            return $this->router->view('site-events/',[
                'active' => 'register',
                'danger' => true
            ]);
        }

        $site_arquivo = $this->siteArquivoRepository->findById($site_evento->site_arquivo_id);

        $validator = new Validator($data);

        $rules = [
            'name' => 'required|min:1|max:100',
            'description' => 'max:255'
        ];

        if(!$validator->validate($rules)){
            return $this->router->view('site-events/edit', [
                'active' => 'register',
                'danger' => true
            ]);
        }

        $data['site_arquivo_id'] = $site_evento->site_arquivo_id;
        $data['id'] = $site_evento->id;

        $updated = $this->siteEventoRepository->updateAll($data, $dir);

        if(is_null($updated)){
            return $this->router->view('site-events/edit', [
                'active' => 'register',
                'danger' => true
            ]);
        }

        return $this->router->redirect('site-eventos');
    }

    public function destroy(Request $request, $id){
        $site_evento = $this->siteEventoRepository->findByUuid($id);

        if(is_null($site_evento)){
            return $this->router->view('site-events/', [
                'active' => 'register',
                'danger' => true
            ]);
        }

        $this->siteEventoRepository->deleteAll($site_evento);
    }

}