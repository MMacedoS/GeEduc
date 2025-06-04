<?php

namespace App\Repositories\Period;

use App\Config\Database;
use App\Interfaces\Period\IPeriodoRepository;
use App\Models\Period\Periodo;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper; 

class PeriodoRepository implements IPeriodoRepository {
    const CLASS_NAME = Periodo::class;
    const TABLE = 'periodo';

    use FindTrait;
    protected $conn;
    protected $model;

    public function __construct(){
        $this->conn = Database::getInstance()->getConnection();
        $this->model = new Periodo();
    }

    public function all(){
        $stmt = $this->conn->query("SELECT * FROM " . self::TABLE . " order by periodo ASC");
        return $stmt->fetchAll(\PDO::FETCH_CLASS, self::CLASS_NAME);
    }

    public function create(array $data){
        $period = $this->model->create(
            $data
        );

        try{
            $stmt = $this->conn
            ->prepare(
                "INSERT INTO " . self::TABLE . "
                    SET
                        uuid = :uuid,
                        periodo = :period"
            );

            $create = $stmt->execute([
                ':uuid' => $period->uuid,
                ':period' => $period->periodo
            ]);

            if(is_null($create)){
                return null;
            }

            return $this->findByUuid($period->uuid);
        } catch(\Throwable $th){
            return null;
        } finally {          
            Database::getInstance()->closeConnection();
        }
    }

    public function update(array $data, int $id){
        $period = $this->model->create($data);

        try{
            $stmt = $this->conn->prepare(
                "UPDATE " . self::TABLE . "
                    SET
                        periodo = :period
                    WHERE id = :id"
            );

            $updated = $stmt->execute([
                ':id' => $id,
                ':period' => $period->periodo
            ]);

            if(!$updated){
                return null;
            }

            return $this->findById($id);
        } catch(\Throwable $th){
            LoggerHelper::logInfo($th->getMessage());
            return null;
        } finally {          
            Database::getInstance()->closeConnection();
        }
    }
}