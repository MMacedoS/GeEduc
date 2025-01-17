<?php

namespace App\Repositories\Bimester;

use App\Config\Database;
use App\Models\Bimester\Bimestre;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper; 

class BimestreRepository{
    const CLASS_NAME = Bimestre::class;
    const TABLE = 'bimestres';

    use FindTrait;
    protected $conn;
    protected $model;

    public function __construct(){
        $conn = new Database();
        $this->conn = $conn->getConnection();
        $this->model = new Bimestre();
    }

    public function allBimesters(){
        $stmt = $this->conn->query("SELECT * FROM " . self::TABLE . " order by bimestre ASC");
        return $stmt->fetchAll(\PDO::FETCH_CLASS, self::CLASS_NAME);
    }

    public function create(array $data){
        $bimestre = $this->model->create(
            $data
        );

        try{
            $stmt = $this->conn
            ->prepare(
                "INSERT INTO " . self::TABLE . "
                    SET
                        uuid = :uuid,
                        bimestre = :bimester"
            );

            $create = $stmt->execute([
                ':uuid' => $bimestre->uuid,
                ':bimester' => $bimestre->bimestre
            ]);

            if(is_null($create)){
                return null;
            }

            return $this->findByUuid($bimestre->uuid);
        }catch(\Throwable $th){
            return null;
        }
    }

    public function update(array $data, int $id){
        $bimestre = $this->model->create($data);

        try{
            $stmt = $this->conn->prepare(
                "UPDATE " . self::TABLE . "
                    SET
                        bimestre = :bimester
                    WHERE id = :id"
            );

            $updated = $stmt->execute([
                ':id' => $id,
                ':bimester' => $bimestre->bimestre
            ]);

            if(!$updated){
                return null;
            }

            return $this->findById($id);
        }catch(\Throwable $th){
            LoggerHelper::logInfo($th->getMessage());
            return null;
        }
    }
}