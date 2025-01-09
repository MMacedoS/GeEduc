<?php

namespace App\Repositories\Coordination;

use App\Config\Database;
use App\Models\Coordination\Coordenacao;
use App\Repositories\Person\PessoaFisicaRepository;
use App\Repositories\Profile\UsuarioRepository;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;

class CoordenacaoRepository {
    const CLASS_NAME = Coordenacao::class;
    const TABLE = 'Coordenacao';

    use FindTrait;
    protected $conn;
    protected $model;
    protected $usuarioRepository;
    protected $pessoaFisicaRepository;

    public function __construct() {
        $conn = new Database();
        $this->conn = $conn->getConnection();
        $this->model = new Coordenacao();
        $this->usuarioRepository = new UsuarioRepository();
        $this->pessoaFisicaRepository = new PessoaFisicaRepository();
    }

    public function allCoordinators(array $params = []){

        $sql = "SELECT
            p.*,(
                SELECT 
                    JSON_OBJECT(
                        'id', pf.id,
                        'nome', pf.nome,
                        'email', pf.email
                    )
                FROM pessoa_fisica pf
                WHERE pf.id = p.person_id
            ) AS pessoa_fisica
            FROM " . self::TABLE . " 
            p LEFT JOIN pessoa_fisica pf ON p.person_id = pf.id
        ";

        $conditions = [];
        $bindings = [];

        if (isset($params['nome'])) {
            $conditions[] = "pf.nome = :nome";
            $bindings[':nome'] = $params['nome'];
        }

        if (isset($params['email'])) {
            $conditions[] = "pf.email = :email";
            $bindings[':email'] = $params['email'];
        }

        if (isset($params['ativo'])) {
            $conditions[] = "p.ativo = :ativo";
            $bindings[':ativo'] = $params['ativo'];
        }

        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute($bindings);

        return $stmt->fetchAll(\PDO::FETCH_CLASS, self::CLASS_NAME);
    }

    public function saveAll(array $data): ?Coordenacao
    {
        
        if (empty($data)) {
            return null;
        }
        
        try {
            $userData = array_merge($data, [
                'password' => 'password',
                'sector' => 'coordenador',
            ]);
    
            $user = $this->usuarioRepository->create($userData);
            
            $personData = array_merge($data, ['usuario_id' => $user->id]);
            
            $person = $this->pessoaFisicaRepository->create($personData);
           
            $coordinatorData = array_merge($data, ['person_id' => $person->id]);
            $coordinator = $this->create($coordinatorData);
           

            return $coordinator;
    
        } catch (\Throwable $th) {
            LoggerHelper::logInfo("Erro na transação create: {$th->getMessage()}");
            LoggerHelper::logInfo("Trace: " . $th->getTraceAsString());
            return null;
        }
    }

    public function create(array $data)
    {
        $coordinator = $this->model->create($data);

        try {
            $stmt = $this->conn->prepare(
                "INSERT INTO " . self::TABLE . " 
                SET 
                    uuid = :uuid,
                    person_id = :person_id,
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
        }
    }

    public function update(array $data, int $id) 
    {
        $class = $this->model->create($data);

        try {
            $stmt = $this->conn->prepare(
                "UPDATE " . self::TABLE . " 
                SET 
                    nome = :nome,
                    turno = :turno,
                    ordem = :ordem,
                    ativo = :ativo
                WHERE id = :id
                "
            );

            $update = $stmt->execute([
                ':nome' => $class->nome,
                ':turno' => $class->turno,
                ':ordem' => $class->ordem,
                ':ativo' => $class->ativo,
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
}