<?php

namespace App\Controllers\v1\SiteAlbum;

use App\Controllers\Controller;
use App\Repositories\SiteAlbum\SiteAlbumRepository;
use App\Repositories\SiteArchive\SiteArquivoRepository;
use App\Request\Request;
use App\Utils\Paginator;
use App\Utils\Validator;

class SiteAlbumController extends Controller {

    protected $siteAlbumRepository;
    protected $siteArquivoRepository;

    public function __construct(){
        parent::__construct();
        $this->siteAlbumRepository = new SiteAlbumRepository();
        $this->siteArquivoRepository = new SiteArquivoRepository();
    }

    public function index(Request $request){
        $site_albuns = $this->siteAlbumRepository->allSiteAlbuns();
        $perPage = 10;
        $currentPage = $request->getParam('page') ? (int)$request->getParam('page') : 1;
        $paginator = new Paginator($site_albuns, $perPage, $currentPage);
        $paginatedBoards = $paginator->getPaginatedItems();

        $data = [
            'site_albuns' => $paginatedBoards,
            'links' => $paginator->links()
        ];

        return $this->router->view('/site-albuns/index', [
            'active' => 'pedagogico',
            'data' => $data
        ]);
    }

    public function create(Request $request){
        return $this->router->view('/site-albuns/create', [
            'active' => 'pedagogico'
        ]);
    }

    public function store(Request $request){
        $data = $request->getBodyParams();

        if(isset($_FILES['arquivo'])){
            $data['arquivo'] = $_FILES['arquivo'];
        }

        $dir = $_SERVER['DOCUMENT_ROOT'] . '/Public/files/site/albuns/';

        $validator = new Validator($data);

        $rules = [
            'name' => 'required|min:1|max:100',
            'arquivo' => 'required',
            'description' => 'max:255'
        ];

        if(!$validator->validate($rules)){
            return $this->router->view('site-albuns/create', [
                'active' => 'pedagogico',
                'errors' => $validator->getErrors()
            ]);
        }

        $created = $this->siteAlbumRepository->saveAll($data, $dir);

        if(is_null($created)){
            return $this->router->view('site-albuns/create', [
                'active' => 'pedagogico', 
                'danger' => true
            ]);
        }

        return $this->router->redirect('site-albuns/');
    }

    public function edit(Request $request, $id){
        $site_album = $this->siteAlbumRepository->findByUuid($id);

        if(is_null($site_album)){
            return $this->router->view('site-albuns/', [
                'active' => 'register',
                'danger' => true
            ]);
        }

        return $this->router->view('site-albuns/edit', [
            'active' => 'register',
            'site_album' => $site_album
        ]);
    }

    public function update(Request $request, $id){
        $data = $request->getBodyParams();

        if(isset($_FILES['arquivo'])){
            $data['arquivo'] = $_FILES['arquivo'];
        }

        $dir = $_SERVER['DOCUMENT_ROOT'] . '/Public/files/site/albuns/';

        $site_album = $this->siteAlbumRepository->findByUuid($id);

        if(is_null($site_album)){
            return $this->router->view('site-albuns/',[
                'active' => 'register',
                'danger' => true
            ]);
        }

        $site_arquivo = $this->siteArquivoRepository->findById($site_album->site_arquivo_id);

        $validator = new Validator($data);

        $rules = [
            'name' => 'required|min:1|max:100',
            'description' => 'max:255'
        ];

        if(!$validator->validate($rules)){
            return $this->router->view('site-albuns/edit', [
                'active' => 'register',
                'danger' => true
            ]);
        }

        $data['site_arquivo_id'] = $site_album->site_arquivo_id;
        $data['id'] = $site_album->id;

        $updated = $this->siteAlbumRepository->updateAll($data, $dir);

        if(is_null($updated)){
            return $this->router->view('site-albuns/edit', [
                'active' => 'register',
                'danger' => true
            ]);
        }

        return $this->router->redirect('site-albuns');
    }

    public function destroy(Request $request, $id){
        $site_album = $this->siteAlbumRepository->findByUuid($id);

        if(is_null($site_album)){
            return $this->router->view('site-albuns/', [
                'active' => 'register',
                'danger' => true
            ]);
        }

        $this->siteAlbumRepository->deleteAll($site_album);
    }
}