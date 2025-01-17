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

    public function allFiles(array $params = []){
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

    public function create(array $data, string $dir){
        $archive = $this->model->create($data);

        $manipulation = publicPath($data['arquivo'], $dir);

        if(!$manipulation){
            return null;
        }

        try{
            $stmt = $this->conn->prepare(
                "INSERT INTO " . self::TABLE . "
                    SET
                        uuid = :uuid,
                        nome_original = :original_name,
                        ext_arquivo = :ext_archive,
                        arquivo = :archive
                "
            );

            $create = $stmt->execute([
                ':uuid' => $archive->uuid,
                ':original_name' => $manipulation['name'],
                ':ext_archive' => $manipulation['ext'],
                ':archive' => $manipulation['new_name']
            ]);

            if(!$create){
                return null;
            }

            return $this->findByUuid($archive->uuid);
        }catch(\Throwable $th){
            return null;
        }
    }

    public function update(array $data, string $dir, int $id){
        $archive = $this->model->create($data);

        $manipulation = publicPath($data['arquivo'], $dir);

        if(!$manipulation){
            return null;
        }

        try {
            $stmt = $this->conn->prepare(
                "UPDATE " . self::TABLE . " 
                    SET
                        nome_original = :original_name,
                        ext_arquivo = :ext_archive,
                        arquivo = :archive
                    WHERE id = :id
                " 
            );

            $create = $stmt->execute([
                ':id' => $id,
                ':original_name' => $manipulation['name'],
                ':ext_archive' => $manipulation['ext'],
                ':archive' => $manipulation['new_name']
            ]);

            if(!$create){
                return null;
            }

            return $this->findByUuid($archive->uuid);
        }catch(\Throwable $th){
            return null;
        }
    }

    public function delete(int $id){
        $stmt = $this->conn->prepare(
            "DELETE FROM " . self::TABLE . " WHERE id = :id"
        );

        $deleted = $stmt->execute(['id' => $id]);

        return $deleted;
    }

}