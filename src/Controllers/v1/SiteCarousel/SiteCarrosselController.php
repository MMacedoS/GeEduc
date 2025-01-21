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

    public function create(Request $request){}

    public function store(Request $request){}

    public function edit(Request $request, $id){}

    public function update(Request $request, $id){}

    public function destroy(Request $request, $id){}

}