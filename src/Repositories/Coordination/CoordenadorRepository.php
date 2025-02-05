<?php

namespace App\Repositories\Coordination;

use App\Config\Database;
use App\Models\Coordination\Coordenador;
use App\Repositories\Person\PessoaFisicaRepository;
use App\Repositories\Profile\UsuarioRepository;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;

class CoordenadorRepository {
    const CLASS_NAME = Coordenador::class;
    const TABLE = 'coordenadores';

    use FindTrait;
    protected $conn;
    protected $model;
    protected $usuarioRepository;
    protected $pessoaFisicaRepository;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
        $this->model = new Coordenador();
        $this->usuarioRepository = new UsuarioRepository();
        $this->pessoaFisicaRepository = new PessoaFisicaRepository();
    }

    public function allCoordinators(array $params = []){

        $sql = "SELECT
            c.*,
                JSON_OBJECT(
                    'nome', pf.nome,
                    'email', pf.email
                ) AS pessoa_fisica
            FROM " . self::TABLE . " c 
            LEFT JOIN pessoa_fisica pf ON pf.id = c.pessoa_fisica_id 
        ";

        $conditions = [];
        $bindings = [];

        if (isset($params['name_email'])) {
            $conditions[] = "(pf.nome LIKE :name_email or pf.email LIKE :name_email)";
            $bindings[':name_email'] = '%' . $params['name_email'] . '%';
        }

        if (isset($params['situation']) && $params['situation'] != "") {
            $conditions[] = "c.ativo = :situation";
            $bindings[':situation'] = $params['situation'];
        }

        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY c.created_at DESC";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute($bindings);

        return $stmt->fetchAll(\PDO::FETCH_CLASS, self::CLASS_NAME);
    }

    public function saveAll(array $data): ?Coordenador
    {
        if (empty($data)) {
            return null;
        }
        
        try {
            $userData = array_merge($data, [
                'password' => 'password',
                'sector' => 'coordenador',
            ]);
            $this->conn->beginTransaction();
            $user = $this->usuarioRepository->create($userData);
            
            $personData = array_merge($data, ['usuario_id' => $user->id]);
            
            $person = $this->pessoaFisicaRepository->create($personData);
           
            $coordinatorData = array_merge($data, ['person_id' => $person->id]);
            $coordinator = $this->create($coordinatorData);
           
            $this->conn->commit();
            return $coordinator;
    
        } catch (\Throwable $th) {
            $this->conn->rollBack();
            LoggerHelper::logInfo("Erro na transação create: {$th->getMessage()}");
            LoggerHelper::logInfo("Trace: " . $th->getTraceAsString());
            return null;
        } finally {          
            Database::getInstance()->closeConnection();
        }
    }

    public function create(array $data): ?Coordenador
    {
        $coordinator = $this->model->create($data);

        try {
            $stmt = $this->conn->prepare(
                "INSERT INTO " . self::TABLE . " 
                SET 
                    uuid = :uuid,
                    pessoa_fisica_id = :person_id,
                    graduacao = :graduacao
                "
            );

            $create = $stmt->execute([
                ':uuid' => $coordinator->uuid,
                ':person_id' => $coordinator->pessoa_fisica_id,
                ':graduacao' => $coordinator->graduacao
            ]);

            if (!$create) {
                return null;
            }

            return $this->findByUuid($coordinator->uuid);
        } catch (\Throwable $th) {
            
            LoggerHelper::logInfo("Erro na transação create: {$th->getMessage()}");
            LoggerHelper::logInfo("Trace: " . $th->getTraceAsString());
            return null;
        } finally {          
            Database::getInstance()->closeConnection();
        }
    }

    public function update(array $data, int $id) : ?Coordenador 
    {
        $coordenador = $this->model->create($data);

        try {
            $stmt = $this->conn->prepare(
                "UPDATE " . self::TABLE . " 
                SET 
                    graduacao = :graduacao,
                    ativo = :ativo
                WHERE id = :id
                "
            );

            $update = $stmt->execute([
                ':graduacao' => $coordenador->graduacao,
                ':ativo' => $coordenador->ativo,
                ':id' => $id
            ]);

            if (!$update) {
                return null;
            }

            return $this->findById($id);
        } catch (\Throwable $th) {
            LoggerHelper::logInfo("Erro na transação create: {$th->getMessage()}");
            LoggerHelper::logInfo("Trace: " . $th->getTraceAsString());
            return null;
        } finally {          
            Database::getInstance()->closeConnection();
        }
    }

    public function updateAll(array $data): ?Coordenador {

        if(empty($data)){
            return null;
        }

        try{    
            $this->conn->beginTransaction();
            $user = $this->usuarioRepository->update($data, (int)$data['usuario_id']);

            if(is_null($user)){
                return null;
            }

            $person = $this->pessoaFisicaRepository->update($data, (int)$data['pessoa_fisica_id']);

            if(is_null($person)){
                return null;
            }
            
            $coordenador = $this->update($data, (int)$data['id']);

            if(is_null($coordenador)){
                return null;
            }
            $this->conn->commit();
            return $coordenador;

        } catch(\Throwable $th) {
            $this->conn->rollBack();
            LoggerHelper::logInfo("Erro na transação update: {$th->getMessage()}");
            LoggerHelper::logInfo("Trace: " . $th->getTraceAsString());
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

    public function deleteAll($coordenador): ?bool {
        try {
            $this->conn->beginTransaction();
            $pessoa_fisica = $this->pessoaFisicaRepository->findById($coordenador->person_id);

            $this->usuarioRepository->delete($pessoa_fisica->usuario_id);

            $this->pessoaFisicaRepository->delete($pessoa_fisica->id);
            $this->conn->commit();
            return $this->delete($coordenador->id);
        } catch(\Throwable $th) {
            $this->conn->rollBack();
            LoggerHelper::logInfo("Erro na transação delete: {$th->getMessage()}");
            LoggerHelper::logInfo("Trace: " . $th->getTraceAsString());
            return null;
        } finally {          
            Database::getInstance()->closeConnection();
        }
    }
}