<?php 

namespace App\Repositories\Student;

use App\Config\Database;
use App\Models\Student\Estudante;
use App\Repositories\Person\PessoaFisicaRepository;
use App\Repositories\Profile\UsuarioRepository;
use App\Repositories\Traits\FindTrait;
use App\Util\LoggerHelper;

class EstudanteRepository {

    const CLASS_NAME = Estudante::class;
    const TABLE = 'estudantes';

    use FindTrait;
    protected $conn;
    protected $model;
    protected $usuarioRepository;
    protected $pessoaFisicaRepository;

    public function __construct(){
        $conn = new Database();
        $this->conn = $conn->getConnection();
        $this->model = new Estudante();
        $this->usuarioRepository = new UsuarioRepository();
        $this->pessoaFisicaRepository = new PessoaFisicaRepository();
    }

    public function allStudents(array $params = []){

        $sql = "SELECT
            p.*(
                SELECT 
                    JSON_OBJECT(
                        'id', pf.id,
                        'nome', pf.nome,
                        'email', pf.email
                    )
                FROM pessoa_fisica pf
                WHERE pf.id = p.pessoa_fisica_id
            ) AS pessoa_fisica
            FROM " . self::TABLE . " 
            p LEFT JOIN pessoa_fisica pf ON p.pessoa_fisica_id = pf.id
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

    public function saveAll(array $data){

        if(empty($data)){
            return null;
        }

        $userData = array_merge($data, [
            'password' => 'escola123',
            'sector' => 'estudante'
        ]);

        $this->conn->beginTrasanction();

        try{

            $user = $this->usuarioRepository->create($userData);
            if(is_null($user)){
                $this->conn->rollBack();
                return null;
            }

            $personData = array_merge($data, ['usuario_id' => $user->id]);
            $person = $this->pessoaFisicaRepository->create($personData);
            if(is_null($person)){
                $this->conn->rollBack();
                return null;
            }

            $studentData = array_merge($data, ['pessoa_fisica_id' => $person->id]);
            $student = $this->create($studentData);
            if(is_null($student)){
                $this->conn->rollback();
            }

            $this->conn->commit();

            return $student;

        } catch(\Throwable $th){
            $this->conn->rollBack();
            return null;
        }
    }

    public function create(array $data){
        
        $estudante = $this->model->create($data);

        try{
            $stmt = $this->conn->prepare(
                "INSERT INTO " . self::TABLE . "
                    SET
                        uuid = :uuid,
                        pessoa_fisica_id = :pessoa_fisica_id
                "
            );

            $create = $stmt->execute([
                ':uuid' => $estudante->uuid,
                ':pessoa_fisica_id' => $pessoa_fisica_id
            ]);

            return $this->findByUuid($estudante->uuid);

        }catch(\Throwable $th){
            LoggerHelper::logInfo("Erro na transação create de estudante: {$th->getMessage()}");
            LoggerHelper::logInfo("Trace: " . $th->getTraceAsString());
            return null;
        }

    }

    public function updateAll(array $data){

        if(empty($data)){
            return null;
        }

        $this->conn->beginTransaction();

        try{    
            $user = $this->usuarioRepository->update($data, $data['usuario_id']);
            if(is_null($user)){
                $this->conn->rollBack();
                return null;
            }

            $person = $this->pessoaFisicaRepostory->update($data, $data['pessoa_fisica_id']);
            if(is_null($user)){
                $this->conn->rollBack();
                return null;
            }

            $estudante = $this->update($data, $data['id']);
            if(is_null($estudante)){
                LoggerHelper::logInfo("erro no update de estudante");
                $this->conn->rollBack();
                return null;
            }

            $this->conn->commit();

            return  $estudante;

        }catch(\Throwable $th){
            $this->conn->rollBack();
            return null;
        }
    }

    public function update(array $data, int $id){
        $estudante = $this->model->create($data);

        try{
            $stmt = $this->conn->prepare(
                "UPDATE " . self::TABLE . "
                    set
                        pessoa_fisica_id = :pessoa_fisica,
                        ativo = :ativo
                    WHERE id = :id
                "
            );

            $updated = $stmt->execute([
                ':pessoa_fisica_id' => $estudante->pessoa_fisica_id,
                ':ativo' => $estudante->ativo,
                ':id' => $id
            ]);

            if(!$updated){
                return null;
            }

            return $this->findById($id);
        }catch(\Throwable $th){
            return null;
        }
    }

    public function deleteAll($estudante){
        $pessoa_fisica = $this->pessoaFisicaRepository->delete($estudante->pessoa_fisica_id);

        $this->usaurioRepository->delete($pessoa_fisica->usuario_id);

        $this->pessoaFisicaRepository->delete($pessoa_fisica->id);

        return $this->delete($estudante->id);
    }

    public function delete(int $id){
        $stmt = $this->conn->prepare(
            "UPDATE" . self::TABLE . "
                SET
                    ativo = 0
                WHERE id = :id
            "
        );

            $updated = $stmt->execute(['id' => $id]);

            return $update;
    }

}