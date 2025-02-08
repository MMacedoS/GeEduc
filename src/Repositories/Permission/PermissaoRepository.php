<?php

namespace App\Repositories\Permission;

use App\Config\Database;
use App\Interfaces\Permission\IPermissaoRepository;
use App\Models\Permission\Permissao;
use App\Repositories\Traits\FindTrait;

class PermissaoRepository implements IPermissaoRepository {
    const CLASS_NAME = Permissao::class;
    const TABLE = 'permissao';
    protected $conn;
    protected $model;
    
    use FindTrait;

    public function __construct() {
        $this->model = new Permissao();
        $this->conn = Database::getInstance()->getConnection();
    }

    public function all(array $params = [])
    {
        $sql = "SELECT * FROM " . self::TABLE;

        $conditions = [];
        $bindings = [];

        if (isset($params['name'])) {
            $conditions[] = "name = :nome";
            $bindings[':nome'] = $params['name'];
        }

        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY name DESC";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute($bindings);

        return $stmt->fetchAll(\PDO::FETCH_CLASS, self::CLASS_NAME);         
    }

    public function create(array $data)
    {   
        $permissao = $this->model->create(
            $data
        );

        try {
            $stmt = $this->conn
            ->prepare(
                "INSERT INTO " . self::TABLE . " 
                  set 
                    uuid = :uuid,
                    name = :name, 
                    description = :description
            ");
            $create = $stmt->execute([
                ':uuid' => $permissao->uuid,
                ':name' => $permissao->name,
                ':description' => $permissao->description
            ]);
    
            if (is_null($create)) {
                return null;
            }
    
            return $this->findById($this->conn->lastInsertId());
        } catch (\Throwable $th) {
            return null;
        } finally {          
            Database::getInstance()->closeConnection();
        }
    }

    public function update(int $id, array $data)
    {
        $user = $this->model
            ->create(
                $data
            );

        $this->conn->beginTransaction();

        try {
            $stmt = $this->conn
                ->prepare(
                    "UPDATE " . self::TABLE . "
                        set 
                        name = :name, 
                        description = :description
                    WHERE id = :id"
                );

            $updated = $stmt->execute([
                'id' => $id,
                'name' => $user->name,
                'description' => $user->description
            ]);

            if (!$updated) {                
                $this->conn
                    ->rollBack();
                return null;
            }

            $this->conn
                ->commit();
                
            return $this->findById($id);
        } catch (\Throwable $th) {
            $this->conn->rollBack();
            return null;
        } finally {          
            Database::getInstance()->closeConnection();
        }
    }

    public function delete($id) 
    {
        $stmt = $this->conn
            ->prepare(
                "DELETE FROM " . self::TABLE . " WHERE id = :id"
            );
        $deleted = $stmt->execute([':id' => $id]);
        
        return $deleted;

    }

    public function allByUser(int $id)
    {
        $stmt = $this->conn->prepare(
            "SELECT permissao.* FROM  " . self::TABLE . "  
            INNER JOIN permissao_as_usuario 
            ON permissao_as_usuario.permissao_id = permissao.id 
            WHERE usuario_id = :id 
            order by name ASC"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetchAll(\PDO::FETCH_CLASS, self::CLASS_NAME);        
    }
}