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

    public function create(Request $request){}

    public function store(Request $request){}

    public function edit(Request $request, $id){}

    public function update(Request $request, $id){}

    public function destroy(Request $request, $id){}
}