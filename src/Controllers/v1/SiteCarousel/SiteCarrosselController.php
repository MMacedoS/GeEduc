<?php

namespace App\Controllers\v1\SiteCarousel;

use App\Controllers\Controller;
use App\Repositories\SiteCarousel\SiteCarrosselRepository;
use App\Repositories\SiteArchive\SiteArquivoRepository;
use App\Request\Request;
use App\Utils\Paginator;
use App\Utils\Validator;

class SiteCarrosselController extends Controller {

    protected $siteCarrosselRepository;
    protected $siteArquivoRepository;

    public function __construct(){
        parent::__construct();
        $this->siteCarrosselRepository = new SiteCarrosselRepository();
        $this->siteArquivoRepository = new SiteArquivoRepository();
    }

    public function index(Request $request){
        $site_carousel = $this->siteCarrosselRepository->allSiteCarousel();
        $perPage = 10;
        $currentPage = $request->getParam('page') ? (int)$request->getParam('page') : 1;
        $paginator = new Paginator($site_carousel, $perPage, $currentPage);
        $paginatedBoards = $paginator->getPaginatedItems();

        $data = [
            'site_carousel' => $paginatedBoards,
            'links' => $paginator->links()
        ];

        return $this->router->view('/site-carousel/index', [
            'active' => 'pedagogico',
            'data' => $data
        ]);
    }

    public function create(Request $request){
        return $this->router->view('site-carousel/create', [
            'active' => 'pedagogico',
        ]);
    }

    public function store(Request $request){
        $data = $request->getBodyParams();

        if(isset($_FILES['arquivo'])){
            $data['arquivo'] = $_FILES['arquivo'];
        }

        $dir = $_SERVER['DOCUMENT_ROOT'] . '/Public/files/site/carousel/';

        $validator = new Validator($data);

        $rules = [
            'name' => 'required|min:1|max:100',
            'arquivo' => 'required',
            'description' => 'max:255',
            'local' => 'max:45'
        ];

        if(!$validator->validate($rules)){
            return $this->router->view('site-carousel/create',[
                'active' => 'pedagogico',
                'errors' => $validator->getErrors()
            ]);
        }

        $created = $this->siteCarrosselRepository->saveAll($data, $dir);

        if(is_null($created)){
            return $this->router->view('site-carousel/create', [
                'active' => 'pedagogico',
                'danger' => true
            ]);
        }

        return $this->router->redirect('site-carrossel/');
    }

    public function edit(Request $request, $id){
        $site_carousel = $this->siteCarrosselRepository->findByUuid($id);

        if(is_null($site_carousel)){
            return $this->router->view('site-carousel/', [
                'active' => 'register',
                'danger' => true
            ]);
        }

        return $this->router->view('site-carousel/edit', [
            'active' => 'register',
            'site_carousel' => $site_carousel
        ]);
    }

    public function update(Request $request, $id){
        $data = $request->getBodyParams();

        if(isset($_FILES['arquivo'])){
            $data['arquivo'] = $_FILES['arquivo'];
        }

        $dir = $_SERVER['DOCUMENT_ROOT'] . '/Public/files/site/carousel/';

        $site_carousel = $this->siteCarrosselRepository->findByUuid($id);

        if(is_null($site_carousel)){
            return $this->router->view('site-carousel/',[
                'active' => 'register',
                'danger' => true
            ]);
        }

        $site_arquivo = $this->siteArquivoRepository->findById($site_carousel->site_arquivo_id);

        $validator = new Validator($data);

        $rules = [
            'name' => 'required|min:1|max:100',
            'description' => 'max:255',
            'local' => 'max:45'
        ];

        if(!$validator->validate($rules)){
            return $this->router->view('site-carousel/edit', [
                'active' => 'register',
                'danger' => true
            ]);
        }

        $data['site_arquivo_id'] = $site_carousel->site_arquivo_id;
        $data['id'] = $site_carousel->id;

        $updated = $this->siteCarrosselRepository->updateAll($data, $dir);

        if(is_null($updated)){
            return $this->router->view('site-carousel/edit', [
                'active' => 'register',
                'danger' => true
            ]);
        }

        return $this->router->redirect('site-carrossel');
    }

    public function destroy(Request $request, $id){
        $site_carousel = $this->siteCarrosselRepository->findByUuid($id);

        if(is_null($site_carousel)){
            return $this->router->view('site-carousel/', [
                'active' => 'register',
                'danger' => true
            ]);
        }

        $this->siteCarrosselRepository->deleteAll($site_carousel);
    }

}