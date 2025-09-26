<?php

namespace App\Repositories\Period;

use App\Config\Database;
use App\Config\SingletonInstance;
use App\Interfaces\Period\IPeriodoRepository;
use App\Models\Period\Periodo;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;

class PeriodoRepository extends SingletonInstance implements IPeriodoRepository
{
    const CLASS_NAME = Periodo::class;
    const TABLE = 'periodo';

    use FindTrait;


    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
        $this->model = new Periodo();
    }

    public function all(array $criteria = ['active' => ''])
    {
        $conditions = [];
        $params = [];
        if (isset($criteria['period'])) {
            $conditions[] = "periodo = :periodo";
            $params[':periodo'] = $criteria['period'];
        }

        if (isset($criteria['active'])) {
            $conditions[] = "ativo like :ativo";
            $params[':ativo'] = '%' . $criteria['active'] . '%';
        }

        if (empty($conditions)) {
            return null;
        }

        $sql = "SELECT * FROM " . self::TABLE . " WHERE " . implode(' AND ', $conditions) . " order by periodo ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(\PDO::FETCH_CLASS, self::CLASS_NAME);
    }

    public function create(array $data)
    {
        $period = $this->model->create(
            $data
        );

        try {
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

            if (is_null($create)) {
                return null;
            }

            return $this->findByUuid($period->uuid);
        } catch (\Throwable $th) {
            return null;
        }
    }

    public function update(array $data, int $id)
    {
        $periodExisting = $this->findById($id);

        $period = $this->model->update($data, $periodExisting);

        try {
            $stmt = $this->conn->prepare(
                "UPDATE " . self::TABLE . "
                    SET
                        periodo = :period,
                        ativo = :active
                    WHERE id = :id"
            );

            $updated = $stmt->execute([
                ':id' => $id,
                ':period' => $period->periodo,
                ':active' => $period->ativo
            ]);

            if (!$updated) {
                return null;
            }

            return $this->findById($id);
        } catch (\Throwable $th) {
            LoggerHelper::logInfo($th->getMessage());
            return null;
        }
    }
}
