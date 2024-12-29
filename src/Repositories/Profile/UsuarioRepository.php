<?php

namespace App\Repositories\Profile;

use App\Config\Database;
use App\Models\Profile\Usuario;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;

class UsuarioRepository {
    const CLASS_NAME = Usuario::class;
    const TABLE = 'usuarios';
    
    use FindTrait;
    protected $conn;
    protected $model;

    public function __construct() {
        $conn = new Database();
        $this->conn = $conn->getConnection();
        $this->model = new Usuario();
    }

    public function all()
    {
        $stmt = $this->conn->query("SELECT * FROM " . self::TABLE . " order by nome ASC");
        return $stmt->fetchAll(\PDO::FETCH_CLASS, self::CLASS_NAME);        
    }

    public function create(array $data)
    {   
        $existingUser = $this->findByEmail($data['email'], $data['sector']);
        if ($existingUser) {
            return $existingUser;
        }

        $user = $this->model->create(
            $data
        );

        try {
            $stmt = $this->conn
            ->prepare(
                "INSERT INTO " . self::TABLE . " 
                  set 
                    uuid = :uuid,
                    nome = :name, 
                    email = :email, 
                    senha = :password,
                    painel = :sector
            ");
            $create = $stmt->execute([
                ':uuid' => $user->uuid,
                ':name' => $user->nome,
                ':email' => $user->email,
                ':password' => md5($user->senha . $user->uuid),
                ':sector' => $user->painel
            ]);
    
            if (is_null($create)) {
                return null;
            }
    
            return $this->findByUuid($user->uuid);
        } catch (\Throwable $th) {
            return null;
        }
    }

    public function findByEmail(string $email, string $sector)
    {
        try {
            $stmt = $this->conn->prepare(
                "SELECT * FROM " . self::TABLE . " WHERE email = :email and painel= :sector LIMIT 1"
            );
            $stmt->execute([':email' => $email, ':sector' => $sector]);
            $stmt->setFetchMode(\PDO::FETCH_CLASS, self::CLASS_NAME);

            return $stmt->fetch() ?: null;
        } catch (\Throwable $th) {
            LoggerHelper::logInfo($th->getMessage());
            return null;
        }
    }

    public function update(array $data, int $id)
    {
        $user = $this->model->create(
            $data
        );

        try {
            $stmt = $this->conn
            ->prepare(
                "UPDATE " . self::TABLE . "
                    set 
                    nome = :name, 
                    email = :email, 
                    ativo = :status,
                    painel = :sector 
                WHERE id = :id"
            );

            $updated = $stmt->execute([
                ':id' => $id,
                ':name' => $user->nome,
                ':email' => $user->email,
                ':sector' => $user->painel,
                ':status' => $user->ativo
            ]);

            if (!$updated) {        
                return null;
            }
            return $this->findById($id);
        } catch (\Throwable $th) {
            return null;
        }
    }

    public function getLogin(string $email, string $senha)
    {
        if (is_null($email) && is_null($senha)) {
            return null;
        }
    
        $stmt = $this->conn->prepare("SELECT id as code, senha, nome, email, ativo, uuid as id FROM " . self::TABLE . " WHERE email = '$email'");
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::CLASS_NAME);
        $user = $stmt->fetch(); 
        if (!$user) {
            return null;
        }  
        
        if(md5($senha . $user->id) !== $user->senha) {
            return null;
        }
        unset($user->uuid);
        unset($user->senha);
        return $user;
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

    public function findPermissions(int $usuario_id) 
    {
        $stmt = $this->conn
            ->prepare(
                "SELECT permissao_as_usuario.* 
                FROM permissao_as_usuario 
                where usuario_id = :usuario_id");
        $stmt->bindValue(':usuario_id', $usuario_id);
        $stmt->execute();
        $user_permissions = $stmt->fetchAll(\PDO::FETCH_ASSOC); 

        return $user_permissions;
    }

    public function addPermissions(array $data, int $id): bool 
    {
        // Verifica se $id é válido e se há permissões para adicionar
        if (empty($data['permissions']) || $id <= 0) {
            return false;
        }

        // Remove permissões existentes
        if (!$this->removePermissions($id)) {
            return false;
        }

        // Adiciona novas permissões
        foreach ($data['permissions'] as $permission) {
            $stmt = $this->conn->prepare(
                "INSERT INTO permissao_as_usuario (permissao_id, usuario_id) 
                VALUES (:permissao_id, :usuario_id)"
            );
            
            $success = $stmt->execute([
                ':permissao_id' => (int)$permission,
                ':usuario_id' => (int)$id
            ]);

            // Retorna false se qualquer inserção falhar
            if (!$success) {
                return false;
            }
        }

        // Retorna true se todas as permissões foram inseridas com sucesso
        return true;
    }

    public function removePermissions(int $usuario_id): bool 
    {
        $stmt = $this->conn->prepare(
            "DELETE FROM permissao_as_usuario WHERE usuario_id = :usuario_id"
        );
        $deleted = $stmt->execute([':usuario_id' => (int)$usuario_id]);

        return $deleted;
    }

}