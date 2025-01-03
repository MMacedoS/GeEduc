<?php

namespace App\Repositories\Discipline;

use App\Config\Database;
use App\Models\Discipline\Disciplina;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;

class DisciplinaRepository{
    const CLASS_NAME = Disciplina::class;
    const TABLE = 'disciplinas';

    use FindTrait;
    protected $conn;
    protected $model;

    public function __construct(){
        $conn = new Database();
        $this->conn = $conn->getConnection();
        $this->model = new Disciplina();
    }

    public function allDisciplines(){
        $stmt = $this->conn->query("SELECT * FROM " . self::TABLE . " order by nome ASC");
        return $stmt->fetchAll(\PDO::FETCH_CLASS, self::CLASS_NAME);
    }

    public function create(array $data){
        $disciplina = $this->model->create(
            $data
        );

        try{
            $stmt = $this->conn
            ->prepare(
                "INSERT INTO " . self::TABLE . "
                    SET
                        uuid = :uuid,
                        nome = :name"
            );

            $create = $stmt->execute([
                ':uuid' => $disciplina->uuid,
                ':name' => $disciplina->nome
            ]);

            if(is_null($create)){
                return null;
            }

            return $this->findByUuid($disciplina->uuid);
        }catch (\Throwable $th) {
            return null;
        }
    }

    public function update(array $data, int $id){
        $disciplina = $this->model->create($data);

        try{
            $stmt = $this->conn->prepare(
                "UPDATE " . self::TABLE . "
                    SET
                        nome = :name,
                        ativo = :status
                    WHERE id = :id"
            );

            $parameters = [
                ':id' => $id,
                ':name' => $disciplina->nome,
                ':status' => $disciplina->ativo
            ];

            $updated = $stmt->execute($parameters);

            if(!updated){
                return null;
            }

            return $this->findById($id);
        }catch (\Throwable $th) {
            LoggerHelper::logInfo($th->getMessage());
            return null;
        }
    }

    public function delete(int $id){
        $stmt = $this->conn
        ->prepare(
            "UPDATE " . self::TABLE . "
                SET 
                    ativo = 0
                WHERE id = :id"
        );

        $updated = $stmt->execute(['id' => $id]);

        return $updated;
    }
}