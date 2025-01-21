<?php

namespace App\Repositories\SiteAlbum;

use App\Config\Database;
use App\Models\SiteAlbum\SiteAlbum;
use App\Repositories\SiteArchive\SiteArquivoRepository;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;

class SiteAlbumRepository{

    const CLASS_NAME = SiteAlbum::class;
    const TABLE = 'site_albuns';

    protected $conn;
    protected $model;
    protected $siteArquivoRepository;

    use FindTrait;

    public function __construct(){
        $this->model = new SiteAlbum();
        $this->conn = Database::getInstance()->getConnection();
        $this->siteArquivoRepository = new SiteArquivoRepository();
    }

    public function allSiteAlbuns(array $params = []){
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
            $site_albumData = array_merge($data, ['site_arquivo_id' => $site_archive->id]);
            $site_album = $this->create($site_albumData);

            return $site_album;
        }catch(\Throwable $th){
            return null;
        }finally{
            Database::getInstance()->closeConnection();
        }
    }

    public function create(array $data){
        $site_album = $this->model->create($data);

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
                ':uuid' => $site_album->uuid,
                ':name' => $site_album->nome,
                ':description' => $site_album->descricao,
                ':site_arquivo_id' => $site_album->site_arquivo_id
            ]);
            
            if(!$create){
                return null;
            }

            return $this->findByUuid($site_album->uuid);
        }catch(\Throwable $th){
            return null;
        }finally{
            Database::getInstance()->closeConnection();
        }
    }
}