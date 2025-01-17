<?php

namespace App\Repositories\Profile;

use App\Config\Database;
use App\Models\Profile\Usuario;
use App\Repositories\Permission\PermissaoRepository;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;

class UsuarioRepository {
    const CLASS_NAME = Usuario::class;
    const TABLE = 'usuarios';
    
    use FindTrait;
    protected $conn;
    protected $model;
    private $permissioRepository;

    public function __construct() {
        $conn = new Database();
        $this->conn = $conn->getConnection();
        $this->model = new Usuario();
        $this->permissioRepository = new PermissaoRepository();
    }

    public function all(array $params = [])
    {
        $sql = "SELECT * FROM " . self::TABLE;

        $conditions = [];
        $bindings = [];

        if (isset($params['name'])) {
            $conditions[] = "nome = :nome";
            $bindings[':nome'] = $params['name'];
        }

        if (isset($params['email'])) {
            $conditions[] = "email = :email";
            $bindings[':email'] = $params['email'];
        }

        if (isset($params['sector'])) {
            $conditions[] = "painel = :painel";
            $bindings[':painel'] = $params['sector'];
        }

        if (isset($params['active'])) {
            $conditions[] = "p.ativo = :ativo";
            $bindings[':ativo'] = $params['active'];
        }

        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY nome DESC";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute($bindings);

        return $stmt->fetchAll(\PDO::FETCH_CLASS, self::CLASS_NAME);  
    }

    public function create(array $data, bool $forceNewPassword = true)
    {   
        $existingUser = $this->findByEmail($data['email'], $data['sector']);
        if ($existingUser) {
            return $existingUser;
        }

        $user = $this->model->create(
            $data,
            $forceNewPassword
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
                ':password' => $user->senha,
                ':sector' => $user->painel
            ]);
    
            if (is_null($create)) {
                return null;
            }

            $userFromDb = $this->findByUuid($user->uuid);
            $this->assignPermissionsToUser($userFromDb);            
            return $userFromDb;
        } catch (\Throwable $th) {
            LoggerHelper::logInfo($th->getMessage());
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
        $existingUser = $this->findById($id);
        if (!$existingUser) {
            return null; 
        }

        $data['existing_password'] = $existingUser->senha;
        $senha = (string)$data['password'];
        $user = $this->model->create($data, !hash_equals($senha, $existingUser->senha));

        try {
            $stmt = $this->conn->prepare(
                "UPDATE " . self::TABLE . "
                    SET 
                        nome = :name, 
                        email = :email, 
                        ativo = :status,
                        painel = :sector,
                        senha = :senha
                    WHERE id = :id"
            );

            $parameters = [
                ':id' => $id,
                ':name' => $user->nome,
                ':email' => $user->email,
                ':sector' => $user->painel,
                ':status' => $user->ativo,
                ':senha' => $user->senha
            ];

            $updated = $stmt->execute($parameters);

            if (!$updated) {
                return null;
            }
            $userFromDb = $this->findById($id);

            $this->assignPermissionsToUser($userFromDb);

            return $userFromDb;
        } catch (\Throwable $th) {
            LoggerHelper::logInfo($th->getMessage());
            return null;
        }
    }

    public function getLogin(string $email, string $senha)
    {
        if (empty($email) || empty($senha)) {
            return null;
        }
    
        $stmt = $this->conn->prepare(
            "SELECT id as code, senha, nome, email, ativo, uuid as id 
             FROM " . self::TABLE . " 
             WHERE email = :email"
        );
        $stmt->bindValue(':email', $email);
        $stmt->execute();
    
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::CLASS_NAME);
        $user = $stmt->fetch();
    
        if (!$user) {
            return null;
        }
    
        if (!password_verify($senha, $user->senha)) {
            return null;
        }
    
        unset($user->uuid, $user->senha);
    
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
        if (empty($data['permissions']) || $id <= 0) {
            return false;
        }

        if (!$this->removePermissions($id)) {
            return false;
        }

        foreach ($data['permissions'] as $permission) {
            $stmt = $this->conn->prepare(
                "INSERT INTO permissao_as_usuario (permissao_id, usuario_id) 
                VALUES (:permissao_id, :usuario_id)"
            );
            
            $success = $stmt->execute([
                ':permissao_id' => (int)$permission,
                ':usuario_id' => (int)$id
            ]);

            if (!$success) {
                return false;
            }
        }

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

    private function assignPermissionsToUser(Usuario $userFromDb)
    {
        $access = $userFromDb->painel !== 'administrativo' ? ['name' => $userFromDb->painel] : [];
        
        $permissions = $this->permissioRepository->all($access);

        if (empty($permissions)) {
            return $userFromDb;
        }

        $permissionIds = array_map(fn($permission) => $permission->id, $permissions);
        
        $this->addPermissions(['permissions' => $permissionIds], $userFromDb->id);

        return $userFromDb;
    }
}