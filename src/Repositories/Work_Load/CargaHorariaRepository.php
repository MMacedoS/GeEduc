<?php

namespace App\Repositories\Work_Load;

use App\Config\Database;
use App\Interfaces\Work_Load\ICargaHorariaRepository;
use App\Models\Work_Load\CargaHoraria;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;

class CargaHorariaRepository implements ICargaHorariaRepository {

    const CLASS_NAME = CargaHoraria::class;
    const TABLE = 'carga_horaria';

    use FindTrait;
    protected $conn;
    protected $model;

    public function __construct(){
        $this->conn = Database::getInstance()->getConnection();
        $this->model = new CargaHoraria();
    }

    public function allWorkLoad(array $params = []){
        $sql = "SELECT c.* FROM " . self::TABLE . " c";

        $conditions = [];
        $bindings = [];

        if (isset($params['load'])) {
            $conditions[] = "c.carga = :carga";
            $bindings[':carga'] = $params['load'];
        }

        if (isset($params['active'])) {
            $conditions[] = "c.ativo = :ativo";
            $bindings[':ativo'] = $params['active'];
        }

        if(count($conditions) > 0){
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY c.created_at DESC";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute($bindings);

        return $stmt->fetchAll(\PDO::FETCH_CLASS, self::CLASS_NAME);
    }   

    public function create(array $data){
        $carga_horaria = $this->model->create($data);

        try{
            $stmt = $this->conn->prepare(
                "INSERT INTO " . self::TABLE . "
                    SET
                        uuid = :uuid,
                        carga = :carga
                "
            );

            $create = $stmt->execute([
                ':uuid' => $carga_horaria->uuid,
                ':carga' => $carga_horaria->carga
            ]);

            if(is_null(!$create)){
                return null;
            }

            return $this->findByUuid($carga_horaria->uuid);

        } catch(\Throwable $th){
            return null;
        } finally {          
            Database::getInstance()->closeConnection();
        }
    }

    public function update(array $data, int $id){
        $carga_horaria = $this->model->create($data);

        try{
            $stmt = $this->conn->prepare(
                "UPDATE " . self::TABLE . "
                    SET
                        carga = :carga,
                        ativo = :ativo
                    WHERE id = :id
                "
            );

            $update = $stmt->execute([
                ':carga' => $carga_horaria->carga,
                ':ativo' => $carga_horaria->ativo,
                ':id' => $id
            ]);

            if(!$update){
                return null;
            }

            return $this->findById($id);

        } catch(\Throwable $th){
            return null;
        } finally {          
            Database::getInstance()->closeConnection();
        }
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