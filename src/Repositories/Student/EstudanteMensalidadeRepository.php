<?php

namespace App\Repositories\Student;

use App\Config\Database;
use App\Interfaces\Student\IEstudanteMensalidadeRepository;
use App\Models\Student\EstudanteMensalidade;
use App\Repositories\MonthlyFees\MensalidadeRepository;
use App\Repositories\Plan\PlanoRepository;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;

class EstudanteMensalidadeRepository implements IEstudanteMensalidadeRepository {
    const CLASS_NAME = EstudanteMensalidade::class;
    const TABLE = 'estudante_mensalidade';

    use FindTrait;
    protected $conn;
    protected $model;
    protected $mensalidadeRepository;
    protected $planoRepository;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
        $this->model = new EstudanteMensalidade();
        $this->mensalidadeRepository = new MensalidadeRepository();
        $this->planoRepository = new PlanoRepository();
    }

    public function allMonthlyfees(array $params = [])
    {
        $sql = "SELECT 
           em.*,
            json_object(
            'id', e.id,
            'uuid', e.uuid,
            'nome', pf.nome
            ) as 'estudantes',
            json_object(
            'id', e.id,
            'uuid', e.uuid,
            'nome', pf.nome,
            'doc', pf.doc,
            'email', pf.email,
            'data_nascimento', pf.data_nascimento,
            'dia_mensalidade', em.dia_mensalidade,
            'valor', m.valor
            ) as contrato_infos
        FROM " . self::TABLE . " em 
        LEFT JOIN estudantes e
        on e.id = em.estudante_id 
        LEFT JOIN pessoa_fisica pf 
        on e.pessoa_fisica_id = pf.id
        LEFT JOIN mensalidades m on m.estudante_mensalidade_id = em.id
        ";
        $conditions = [];
        $bindings = [];
        
        if (isset($params['active'])) {
            $conditions[] = "m.ativo = :ativo";
            $bindings[':ativo'] = $params['active'];
        }

        if (isset($params['start_date']) && isset($params['end_date'])) {
            $conditions[] = "m.created_at between :start_date and :end_date";
            $bindings[':start_date'] = $params['start_date'];
            $bindings[':end_date'] = $params['end_date'];
        }

        if (isset($params['student_id'])) {
            $conditions[] = "m.estudante_id = :estudante_id";
            $bindings[':estudante_id'] = $params['student_id'];
        }

        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY m.created_at DESC";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute($bindings);

        return $stmt->fetchAll(\PDO::FETCH_CLASS);        
    }

    public function create(array $data)
    {
        $monthly = $this->model->create($data);

        try {
            $stmt = $this->conn->prepare(
                "INSERT INTO " . self::TABLE . " 
                SET 
                    uuid = :uuid,
                    estudante_id = :estudante_id,
                    desconto = :desconto,
                    dia_mensalidade = :dia_mensalidade,
                    plano_id = :plano_id
                "
            );

            $create = $stmt->execute([
                ':uuid' => $monthly->uuid,
                ':estudante_id' => $monthly->estudante_id,
                ':desconto' => $monthly->desconto,
                ':dia_mensalidade' => $monthly->dia_mensalidade,
                ':plano_id' => $monthly->plano_id
            ]);

            if (!$create) {
                return null;
            }

            $plano = $this->planoRepository->findById($monthly->plano_id);

            $monthly = $this->findByUuid($monthly->uuid);            
            $data['studante_monthly_id'] = $monthly->id;
            $data['monthly_day'] = $monthly->dia_mensalidade;
            $data['expiration_date'] = Date('Y-m-') . $monthly->dia_mensalidade;
            $data['amount'] = $plano->valor;
            $monthlyfees = $this->mensalidadeRepository->create($data);

            if(is_null($monthlyfees)){
                return null;
            }
            
            return $monthly;
        } catch (\Throwable $th) {
            return null;
        } finally {          
            Database::getInstance()->closeConnection();
        }
    }

    public function update(array $data, int $id)
    {
        $monthly = $this->findById($id);

        if (is_null($monthly)) {
            return null;
        }

        $monthly = $monthly->update($data, $monthly);

        try {
            $stmt = $this->conn->prepare(
                "UPDATE " . self::TABLE . " 
                SET 
                    desconto = :desconto,
                    dia_mensalidade = :dia_mensalidade,
                    plano_id = :plano_id
                WHERE id = :id
                "
            );

            $update = $stmt->execute([
                ':id' => $id,
                ':desconto' => $monthly->desconto,
                ':dia_mensalidade' => $monthly->dia_mensalidade,
                ':plano_id' => $monthly->plano_id
            ]);

            if (!$update) {
                return null;
            }

            $monthly = $this->findByUuid($monthly->uuid);            

            if(is_null($monthly)){
                return null;
            }

            return $monthly;
        } catch (\Throwable $th) {
            return null;
        } finally {          
            Database::getInstance()->closeConnection();
        }
    }

    public function delete(int $id)
    {
        $stmt = $this->conn
        ->prepare(
            "UPDATE " . self::TABLE . " 
             SET ativo = 0 
             WHERE id = :id"
        );

        $updated = $stmt->execute(['id' => $id]);

        return $updated;
    }

    public function remove($id) :?bool 
    {
        $estudanteMensalidade = $this->findById((int)$id);

        if (is_null($estudanteMensalidade)) {
            return null;
        }
        
        try {
            $stmt = $this->conn->prepare("DELETE FROM " . self::TABLE . " WHERE id = :id");
            $delete = $stmt->execute([
                ':id' => $id
            ]);
            
            if($delete) {
                return true;
            }
            return false;
        } catch(\Throwable $th) {
            LoggerHelper::logInfo("Erro na transação delete: {$th->getMessage()}");
            LoggerHelper::logInfo("Trace: " . $th->getTraceAsString());
            return null;
        } finally {          
            Database::getInstance()->closeConnection();
        }
    }

    public function getMonthlyFee(array $params = []): ?EstudanteMensalidade
    {
        try {
            // Base SQL
            $sql = "SELECT em.* FROM " . self::TABLE . " em";
            
            // Inicializa condições e bindings
            $conditions = [];
            $bindings = [];
    
            // Condições dinâmicas
            if (!empty($params['active'])) {
                $conditions[] = "em.ativo = :ativo";
                $bindings[':ativo'] = $params['active'];
            }
    
            if (!empty($params['plan_id'])) {
                $conditions[] = "em.plano_id = :plano_id";
                $bindings[':plano_id'] = $params['plan_id'];
            }
    
            if (!empty($params['student_id'])) {
                $conditions[] = "em.estudante_id = :estudante_id";
                $bindings[':estudante_id'] = $params['student_id'];
            }
    
            // Adiciona condições ao SQL
            if ($conditions) {
                $sql .= " WHERE " . implode(" AND ", $conditions);
            }
    
            $sql .= " ORDER BY em.created_at DESC LIMIT 1";
    
            // Prepara e executa a consulta
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($bindings);
    
            // Configura o modo de retorno
            $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::CLASS_NAME);
            $result = $stmt->fetch(); // Retorna o resultado
            return $result !== false ? $result : null;
        } catch (\Throwable $th) {
            return null;
        } finally {          
            Database::getInstance()->closeConnection();
        }
    }
    
}