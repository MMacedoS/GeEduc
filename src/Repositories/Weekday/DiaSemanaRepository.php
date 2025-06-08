<?php

namespace App\Repositories\Weekday;

use App\Config\Database;
use App\Config\SingletonInstance;
use App\Interfaces\Weekday\IDiaSemanaRepository;
use App\Models\Weekdays\DiaSemana;
use App\Repositories\Traits\FindTrait;

class DiaSemanaRepository extends SingletonInstance implements IDiaSemanaRepository 
{   
    const CLASS_NAME = DiaSemana::class;
    const TABLE = 'dias_da_semana';

    use FindTrait;

    public function __construct(){
        $this->conn = Database::getInstance()->getConnection();
        $this->model = new DiaSemana();
    }
    
    public function allWeekDay(array $params = [])
    {        
        $sql = "SELECT ds.* FROM " . self::TABLE . " ds";

        $conditions = [];
        $bindings = [];

        if (isset($params['day'])) {
            $conditions[] = "ds.dia = :dia";
            $bindings[':dia'] = $params['day'];
        }

        if(count($conditions) > 0){
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY ds.dia, ds.horario, ds.turno ASC";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute($bindings);

        return $stmt->fetchAll(\PDO::FETCH_CLASS, self::CLASS_NAME);
    }
}