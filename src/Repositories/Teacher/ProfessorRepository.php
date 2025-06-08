<?php

namespace App\Repositories\Teacher;

use App\Config\Database;
use App\Config\SingletonInstance;
use App\Interfaces\Teacher\IProfessorRepository;
use App\Models\Teacher\Professor;
use App\Repositories\Person\PessoaFisicaRepository;
use App\Repositories\Profile\UsuarioRepository;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;

class ProfessorRepository extends SingletonInstance implements IProfessorRepository {
    const CLASS_NAME = Professor::class;
    const TABLE = 'professores';
    
    use FindTrait;

    protected $usuarioRepository;
    protected $pessoaFisicaRepository;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
        $this->model = new Professor();
        $this->usuarioRepository = UsuarioRepository::getInstance(); 
        $this->pessoaFisicaRepository = PessoaFisicaRepository::getInstance(); 
    }

    public function allTeachers(array $params = [])
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
        
        if (isset($params['name_email'])) {
            $conditions[] = "(pf.nome LIKE :name_email OR pf.email LIKE :name_email)";
            $bindings[':name_email'] = "%" .  $params['name_email'] . "%";
        }

        if (isset($params['situation']) && $params['situation'] != '') {
            $conditions[] = "p.ativo = :situation";
            $bindings[':situation'] = $params['situation'];
        }

        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute($bindings);

        return $stmt->fetchAll(\PDO::FETCH_CLASS, self::CLASS_NAME);        
    }

    public function teacherWithPersonByUuid(string $uuid){
        try {
            $sql = "SELECT
                e.*,(
                    SELECT 
                        JSON_OBJECT(
                            'id', pf.id,
                            'nome', pf.nome,
                            'email', pf.email
                        )
                    FROM pessoa_fisica pf
                    WHERE pf.id = e.pessoa_fisica_id
                ) AS pessoa_fisica
                FROM " . self::TABLE . " 
                e LEFT JOIN pessoa_fisica pf ON e.pessoa_fisica_id = pf.id
                WHERE e.uuid = :id
            ";
    
            $sql .= " ORDER BY e.created_at DESC LIMIT 1";
    
            $stmt = $this->conn->prepare($sql);
    
            $stmt->execute([':id' => $uuid]);
    
            $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::CLASS_NAME);
            return $stmt->fetch();  
        }catch (\Throwable $th) {
            LoggerHelper::logError($th->getMessage());
            return null;
        } finally {          
            Database::getInstance()->closeConnection();
        }
    }

    public function teacherWithPersonByID(?int $id) {
        if (is_null($id)) {
            return null;
        }
        
        try {
            $sql = "SELECT p.*, 
                    JSON_OBJECT(
                        'id', p.id,
                        'uuid', p.uuid,
                        'nome', pf.nome
                    ) AS professor_details
                    FROM ". self::TABLE ." p
                    LEFT JOIN pessoa_fisica pf ON pf.id = p.pessoa_fisica_id
                    WHERE pf.id = :id";
                    
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['id' => $id]);
            return $stmt->fetch();  
        } catch (\Throwable $th) {
            LoggerHelper::logError($th->getMessage());
            return null;
        } finally {          
            Database::getInstance()->closeConnection();
        }
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
            LoggerHelper::logInfo($th->getMessage());
            $this->conn->rollBack();
            return null;
        } finally {          
            Database::getInstance()->closeConnection();
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
        } finally {          
            Database::getInstance()->closeConnection();
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

            if (is_null($user)) {
                $this->conn->rollBack();
                return null;
            }

            $person = $this->pessoaFisicaRepository->update($data, $data['pessoa_fisica_id']);
            if (is_null($person)) {
                $this->conn->rollBack();
                return null;
            }

            $teacher = $this->update($data, $data['id']);
            if (is_null($teacher)) {
                LoggerHelper::logInfo("erro no reacher");
                $this->conn->rollBack();
                return null;
            }

            $this->conn->commit();

            return $teacher;

        } catch (\Throwable $th) {
            $this->conn->rollBack();
            return null;
        } finally {          
            Database::getInstance()->closeConnection();
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
                    graduacao = :graduacao,
                    pessoa_fisica_id = :pessoa_fisica_id,
                    ativo = :ativo
                WHERE id = :id"
            );

            $updated = $stmt->execute([
                ':graduacao' => $professor->graduacao,
                ':pessoa_fisica_id' => $professor->pessoa_fisica_id,
                ':ativo' => $professor->ativo,
                ':id' => $id
            ]);

            if (!$updated) {        
                return null;
            }

            return $this->findById($id);
        } catch (\Throwable $th) {
            return null;
        } finally {          
            Database::getInstance()->closeConnection();
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

    public function teacherByPersonId(int $person_id){

        $sql = "SELECT
            p.*
            FROM " . self::TABLE . " p
            WHERE p.pessoa_fisica_id = :id
        ";

        $sql .= " ORDER BY p.created_at DESC LIMIT 1";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([':id' => $person_id]);

        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::CLASS_NAME);
        return $stmt->fetch();  
    }
}