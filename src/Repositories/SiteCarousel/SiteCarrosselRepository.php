<?php

namespace App\Repositories\SiteCarousel;

use App\Config\Database;
use App\Models\Site\Carousel\SiteCarrossel;
use App\Repositories\SiteArchive\SiteArquivoRepository;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;

class SiteCarrosselRepository {

    const CLASS_NAME = SiteCarrossel::class;
    const TABLE = 'site_carousel';

    protected $conn;
    protected $model;
    protected $siteArquivoRepository;

    use FindTrait;

    public function __construct(){
        $this->model = new SiteCarrossel();
        $this->conn = Database::getInstance()->getConnection();
        $this->siteArquivoRepository = new SiteArquivoRepository();
    }

    public function allSiteCarousel(array $params = []){
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
            $site_carouselData = array_merge($data, ['site_arquivo_id' => $site_archive->id]);
            $site_carousel = $this->create($site_carouselData);

            return $site_carousel;

        }catch(\Throwable $th){
            return null;
        }finally{
            Database::getInstance()->closeConnection();
        }
    }

    public function create(array $data){
        $site_carousel = $this->model->create($data);
        
        try{
            $stmt = $this->conn->prepare(
                "INSERT INTO " . self::TABLE . "
                    SET
                        uuid = :uuid,
                        nome = :name,
                        descricao = :description,
                        local = :local,
                        link = :link,
                        site_arquivo_id = :site_arquivo_id
                "
            );

            $create = $stmt->execute([
                ':uuid' => $site_carousel->uuid,
                ':name' => $site_carousel->nome,
                ':description' => $site_carousel->descricao,
                ':local' => $site_carousel->local,
                ':link' => $site_carousel->link,
                ':site_arquivo_id' => $site_carousel->site_arquivo_id
            ]);

            if(!$create){
                return null;
            }

            return $this->findByUuid($site_carousel->uuid);
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

            $site_carousel = $this->update($data, $data['id']);

            if(is_null($site_carousel)){
                return null;
            }

            return $site_carousel;
        }catch(\Throwable $th){
            return null;
        }finally{
            Database::getInstance()->closeConnection();
        }
    }


    public function update(array $data, int $id){
        $site_carousel = $this->model->create($data);

        try{
            $stmt = $this->conn->prepare(
                "UPDATE " . self::TABLE . "
                    SET
                        nome = :name,
                        descricao = :description,
                        local = :local,
                        ativo = :active,
                        link = :link
                    WHERE id = :id
                "
            );

            $updated = $stmt->execute([
                ':name' => $site_carousel->nome,
                ':description' => $site_carousel->descricao,
                ':local' => $site_carousel->local,
                ':active' => $site_carousel->ativo,
                ':link' => $site_carousel->link,
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

    public function deleteAll($site_carousel){
        $site_archive = $this->siteArquivoRepository->findById($site_carousel->site_arquivo_id);

        return $this->delete($site_carousel->id);
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