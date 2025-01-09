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

    public function allSiteArchives(array $params = []){
        $sql = "SELECT * FROM " . self::TABLE;

        $conditions = [];
        $bindings = [];

        if (isset($params['original_name'])) {
            $conditions[] = "nome_original = :nome_original";
            $bindings[':nome_original'] = $params['original_name'];
        }

        if (isset($params['ext_archive'])) {
            $conditions[] = "ext_arquivo = :ext_arquivo";
            $bindings[':ext_arquivo'] = $params['ext_archive'];
        }

        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY nome DESC";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute($bindings);

        return $stmt->fetchAll(\PDO::FETCH_CLASS, self::CLASS_NAME);  
    }

    public function create(array $data){
        $archive = $this->model->create($data);

        
    }

    public function update(array $data, int $id){}

    public function delete(int $id){}

}