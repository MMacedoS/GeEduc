<?php

namespace App\Repositories\Calendar;

use App\Config\Database;
use App\Config\SingletonInstance;
use App\Interfaces\Calendar\IDiaLetivoRepository;
use App\Models\Calendar\DiaLetivo;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;
use PDO;

class DiaLetivoRepository extends SingletonInstance implements IDiaLetivoRepository
{
    const CLASS_NAME = DiaLetivo::class;
    const TABLE = 'dias_letivos';

    use FindTrait;

    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
        $this->model = new DiaLetivo();
    }

    public function all(array $params = [])
    {
        $sql = "SELECT * FROM " . self::TABLE;
        $conditions = [];
        $bindings = [];

        if (isset($params['ativo'])) {
            $conditions[] = "ativo = :ativo";
            $bindings[':ativo'] = $params['ativo'];
        }

        if (isset($params['data'])) {
            $conditions[] = "data = :data";
            $bindings[':data'] = $params['data'];
        }

        if (isset($params['year'])) {
            $conditions[] = "(YEAR(data) = :year)";
            $bindings[':year'] = $params['year'];
        }   

        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        $sql .= " ORDER BY data DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($bindings);

        return $stmt->fetchAll(PDO::FETCH_CLASS, self::CLASS_NAME);
    }

    public function firstDay(array $params = []): ?DiaLetivo
    {
        $sql = "SELECT * FROM " . self::TABLE;
        $conditions = [];
        $bindings = [];

        if (isset($params['ativo'])) {
            $conditions[] = "ativo = :ativo";
            $bindings[':ativo'] = $params['ativo'];
        }

        if (isset($params['year'])) {
            $conditions[] = "(YEAR(data) = :year)";
            $bindings[':year'] = $params['year'];
        }   

        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        $sql .= " ORDER BY data ASC limit 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($bindings);

        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::CLASS_NAME);
        $register = $stmt->fetch();  

        return $register;
    }

    public function create(array $data): ?DiaLetivo
    {
        $dia = $this->model->create($data);

        try {
            $stmt = $this->conn->prepare(
                "INSERT INTO " . self::TABLE . " 
                SET uuid = :uuid, data = :data, ativo = :ativo, evento = :evento, created_at = NOW()"
            );

            $result = $stmt->execute([
                ':uuid' => $dia->uuid,
                ':data' => $dia->data,
                ':ativo' => $dia->ativo,
                ':evento' => $dia->evento
            ]);

            if (!$result) return null;

            return $this->findByUuid($dia->uuid);
        } catch (\Throwable $th) {
            LoggerHelper::logInfo("Erro ao criar Dia Letivo: " . $th->getMessage());
            return null;
        } finally {
            Database::getInstance()->closeConnection();
        }
    }

    public function delete(int $id)
    {
        try {
            $stmt = $this->conn->prepare(
                "DELETE FROM " . self::TABLE . "
                 WHERE id = :id"
            );

            return $stmt->execute([':id' => $id]);
        } finally {
            Database::getInstance()->closeConnection();
        }
    }
}
