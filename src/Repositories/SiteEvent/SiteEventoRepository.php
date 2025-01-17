<?php

namespace App\Repositories\SiteEvent;

use App\Config\Database;
use App\Models\SiteEvent\SiteEvento;
use App\Repositories\SiteArchive\SiteArquivoRepository;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;

class SiteEventoRepository {

    const CLASS_NAME = SiteEvento::class;
    const TABLE = 'site_eventos';

    use FindTrait;

    protected $conn;
    protected $model;
    protected $siteArquivoRepository;

    public function __construct(){
        $conn = new Database();
        $this->conn = $conn->getConnection();
        $this->model = new SiteEvento();
        $this->siteArquivoRepository = new SiteArquivoRepository();
    }

    public function allSiteEvents(array $params  = []){
        $sql = "SELECT * FROM " . self::TABLE;

        $conditions = [];
        $bindings = [];

        if(isset($params['name'])){
            $conditions[] = "name = :nome";
            $bindings[':nome'] = $params['name'];
        }

        if(count($conditions) > 0){
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $slq .= " ORDER BY name DESC";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute($bindings);

        return $stmt->fetchAll(\PDO::FETCH_CLASS, selff::CLASS_NAME);
    }

    public function saveAll(array $data, string $dir){
        if(empty($data)){
            return null;
        }

        try{
            $site_archive = $this->siteArquivoRepository->create($data, $dir);

            $site_eventoData = array_merge($data, ['site_arquivo_id' => $site_archive->id]);
            
            $site_evento = $this->create($site_eventoData);

            return $site_evento;

        }catch(\Throwable $th){
            return null;
        }
    }

    public function create(array $data){
        $site_evento = $this->model->create($data);

        try{
            $stmt = $this->conn->prepare(
                "INSERT INTO " . self::TABLE . "
                    SET
                        uuid = :uuid,
                        nome = :name,
                        descricao = :description
                "
            );

            $create = $stmt->execute([
                ':uuid' => $site_evento->uuid,
                ':name' => $site_evento->nome,
                ':description' => $site_evento->descricao
            ]);
            
            if(is_null($create)){
                return null;
            }

            return $this->findByUuid($site_evento->uuid);
        }catch(\Throwable $th){
            return null;
        }
    }

    public function updateAll(array $data, string $dir){
        if(empty($data)){
            return null;
        }

        try{
            $archive = $this->siteArquivoRepository->update($data['arquivo'], $dir, $data['site_arquivo_id']);
            if(is_null($archive)){
                return null;
            }

            $site_evento = $this->update($data, $data['id']);
            if(is_null($site_evento)){
                return null;
            }

            return $site_evento;
        }catch(\Throwable $th){
            return null;
        }
    }

    public function update(array $data, int $id){
        $site_evento = $this->findById($id);

        $site_evento = $this->model->create($data);

        try{
            $stmt = $this->conn->prepare(
                "UPDATE " . self::TABLE . "
                    set 
                        nome = :name,
                        descricao = :description
                        ativo = :active
                    WHERE id = :id
                "
            );

            $updated = $stmt->execute([
                ':name' => $site_evento->nome,
                ':description' => $site_evento->descricao,
                ':active' => $site_evento->ativo
            ]);

            if(!$updated){
                return null;
            }

            return $this->findById($id);

        }catch(\Throwable $th){
            return null;
        }
    }

    public function deleteAll($site_evento){
        $site_archive = $this->siteArquivoRepository->findById($site_evento->site_arquivo_id);

        $this->siteArquivoRepository->delete($site_evento->site_arquivo_id);

        return $this->delete($site_evento->id);
    }

    public function delete(int $id){
        $stmt = $this->conn->prepare(
            "UPDATE " . self::TABLE . "
                SET
                    ativo = 0
                WHERE id = :id
            "
        );

        $updated = $stmt->execute(['id' => $id]);

        return $updated;
    }
}