<?php

namespace App\Repositories\SiteArchive;

use App\Config\Database;
use App\Models\SiteArchive\SiteArquivo;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;

class SiteArquivoRepository {

    const CLASS_NAME = Turma::class;
    const TABLE = 'site_arquivos';

    use FindTrait;

    protected $conn;
    protected $model;

    public function __construct(){
        $conn = new Database();
        $this->conn = $conn->getConnection();
        $this->model = new SiteArquivo();
    }

    public function allSiteArchives(array $params){}

    public function create(array $data){}

    public function update(array $data, int $id){}

    public function delete(int $id){}

}