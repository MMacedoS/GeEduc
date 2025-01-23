<?php

namespace App\Repositories\Site\Event;

use App\Config\Database;
use App\Models\Site\Event\SiteEvento;
use App\Repositories\Site\Archive\SiteArquivoRepository;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;

class SiteEventoRepository {

    const CLASS_NAME = SiteEvento::class;
    const TABLE = 'site_eventos';

    protected $conn;
    protected $model;
    protected $siteArquivoRepository;

    use FindTrait;

    public function __construct(){
        $this->model = new SiteEvento();
        $this->conn = Database::getInstance()->getConnection();
        $this->siteArquivoRepository = new SiteArquivoRepository();
    }

    public function allSiteEvents(array $params  = []){
        $sql = "SELECT * FROM " . self::TABLE;

        $conditions = [];
        $bindings = [];

        if (isset($params['name'])) {
            $conditions[] = "name = :nome";
            $bindings[':nome'] = $params['name'];
        }

        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute($bindings);

        return $stmt->fetchAll(\PDO::FETCH_CLASS, self::CLASS_NAME); 
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
        }finally{
            Database::getInstance()->closeConnection();
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
                        descricao = :description,
                        site_arquivo_id = :site_arquivo_id
                "
            );

            $create = $stmt->execute([
                ':uuid' => $site_evento->uuid,
                ':name' => $site_evento->nome,
                ':description' => $site_evento->descricao,
                ':site_arquivo_id' => $site_evento->site_arquivo_id
            ]);
            
            if(!$create){
                return null;
            }

            return $this->findByUuid($site_evento->uuid);
        }catch(\Throwable $th){
            return null;
        }finally{
            Database::getInstance()->closeConnection();
        }
    }

    public function updateAll(array $data, string $dir){
        if(empty($data)){
            return null;
        }

        try{
            if(!empty($data['arquivo']['name'])){
                $archive = $this->siteArquivoRepository->update($data, $dir, $data['site_arquivo_id']);
            }

            $site_evento = $this->update($data, $data['id']);
            if(is_null($site_evento)){
                return null;
            }

            return $site_evento;
        }catch(\Throwable $th){
            return null;
        }finally{
            Database::getInstance()->closeConnection();
        }
    }

    public function update(array $data, int $id){
        $site_evento = $this->model->create($data);

        try{
            $stmt = $this->conn->prepare(
                "UPDATE " . self::TABLE . "
                    set 
                        nome = :name,
                        descricao = :description,
                        ativo = :active
                    WHERE id = :id
                "
            );

            $updated = $stmt->execute([
                ':name' => $site_evento->nome,
                ':description' => $site_evento->descricao,
                ':active' => $site_evento->ativo,
                ':id' => $id
            ]);

            if(!$updated){
                return null;
            }

            return $this->findById($id);
            
        }catch(\Throwable $th){
            return null;
        }finally{
            Database::getInstance()->closeConnection();
        }
    }

    public function deleteAll($site_evento){
        $site_archive = $this->siteArquivoRepository->findById($site_evento->site_arquivo_id);

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