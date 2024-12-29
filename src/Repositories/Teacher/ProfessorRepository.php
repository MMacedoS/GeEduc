<?php

namespace App\Repositories\Teacher;

use App\Config\Database;
use App\Models\Teacher\Professor;
use App\Repositories\Person\PessoaFisicaRepository;
use App\Repositories\Profile\UsuarioRepository;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;

class ProfessorRepository {
    const CLASS_NAME = Professor::class;
    const TABLE = 'professores';
    
    use FindTrait;
    protected $conn;
    protected $model;
    protected $usuarioRepository;
    protected $pessoaFisicaRepository;

    public function __construct() {
        $conn = new Database();
        $this->conn = $conn->getConnection();
        $this->model = new Professor();
        $this->usuarioRepository = new UsuarioRepository(); 
        $this->pessoaFisicaRepository = new PessoaFisicaRepository(); 
    }

    public function allTeachers()
    {
        $sql = "SELECT 
           p.*,
           (
            SELECT 
               JSON_OBJECT(
                   'id', pf.id,
                   'nome', pf.nome,
                   'email', pf.email
                )
            FROM pessoa_fisica pf
            WHERE pf.id = p.pessoa_fisica_id
        ) AS pessoa_fisica
        FROM " . self::TABLE . " p 
        LEFT JOIN pessoa_fisica pf ON p.pessoa_fisica_id = pf.id   
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

    public function saveAll(array $data) 
    {
        if (empty($data)) {
            return null;
        }
    
        $userData = array_merge($data, [
            'password' => 'escola123',
            'sector' => 'professor'
        ]);
    
        $this->conn->beginTransaction();
    
        try {
            $user = $this->usuarioRepository->create($userData);
            if (is_null($user)) {
                $this->conn->rollBack();
                return null;
            }
    
            $personData = array_merge($data, ['usuario_id' => $user->id]);
            $person = $this->pessoaFisicaRepository->create($personData);
            if (is_null($person)) {
                $this->conn->rollBack();
                return null;
            }
    
            $teacherData = array_merge($data, ['pessoa_fisica_id' => $person->id]);
            $teacher = $this->create($teacherData);
            if (is_null($teacher)) {
                $this->conn->rollBack();
                return null;
            }
    
            $this->conn->commit();
    
            return $teacher;
    
        } catch (\Throwable $th) {
            $this->conn->rollBack();
            return null;
        }
    }

    public function create(array $data)
    {
        $professor = $this->model->create($data);

        try {
            $stmt = $this->conn->prepare(
                "INSERT INTO " . self::TABLE . " 
                SET 
                    uuid = :uuid,
                    graduacao = :graduacao,
                    pessoa_fisica_id = :pessoa_fisica_id
                "
            );

            $create = $stmt->execute([
                ':uuid' => $professor->uuid,
                ':graduacao' => $professor->graduacao,
                ':pessoa_fisica_id' => $professor->pessoa_fisica_id
            ]);

            if (!$create) {
                return null;
            }

            return $this->findByUuid($professor->uuid);
        } catch (\Throwable $th) {
            LoggerHelper::logInfo("Erro na transação create: {$th->getMessage()}");
            LoggerHelper::logInfo("Trace: " . $th->getTraceAsString());
            return null;
        }
    }

    public function updateAll(array $data) 
    {
        if (empty($data)) {
            return null;
        }

        $this->conn->beginTransaction();

        try {
            $user = $this->usuarioRepository->update($data, $data['usuario_id']);

            LoggerHelper::logInfo(json_encode($user));
            if (is_null($user)) {
                $this->conn->rollBack();
                return null;
            }

            LoggerHelper::logInfo(json_encode($user));

            $person = $this->pessoaFisicaRepository->update($data, $data['pessoa_fisica_id']);
            if (is_null($person)) {
                $this->conn->rollBack();
                return null;
            }

            $teacher = $this->update($data, $data['id']);
            if (is_null($teacher)) {
                $this->conn->rollBack();
                return null;
            }

            $this->conn->commit();

            return $teacher;

        } catch (\Throwable $th) {
            LoggerHelper::logInfo("Erro na transação updateAll: {$th->getMessage()}");
            LoggerHelper::logInfo("Trace: " . $th->getTraceAsString());
            $this->conn->rollBack();
            return null;
        }
    }


    public function update(array $data, int $id)
    {
        $professor = $this->model->create(
            $data
        );

        try {
            $stmt = $this->conn
            ->prepare(
                "UPDATE " . self::TABLE . "
                    set 
                     id = :id,
                    uuid = :uuid,
                    graduacao = :graduacao,
                    matricula = :matricula,
                    pessoa_fisica_id = :pessoa_fisica_id,
                    ativo = :ativo
                    updated_at = NOW()
                WHERE id = :id"
            );

            $updated = $stmt->execute([
                ':uuid' => $professor->uuid,
                ':graduacao' => $professor->graduacao,
                ':matricula' => $professor->matricula,
                ':pessoa_fisica_id' => $professor->pessoa_fisica_id,
                ':ativo' => $professor->ativo,
            ]);

            if (!$updated) {        
                return null;
            }

            return $this->findById($id);
        } catch (\Throwable $th) {
            return null;
        }
    }

    public function deleteAll($professor) 
    {
        $pessoa_fisica = $this->pessoaFisicaRepository->findById($professor->pessoa_fisica_id);
        
        $this->usuarioRepository->delete($pessoa_fisica->usuario_id);

        $this->pessoaFisicaRepository->delete($pessoa_fisica->id);
        
        return $this->delete($professor->id);
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