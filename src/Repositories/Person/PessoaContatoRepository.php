<?php 

namespace App\Repositories\Person;

use App\Config\Database;
use App\Models\Person\PessoaContato;
use App\Repositories\Person\PessoaFisicaRepository;
use App\Repositories\Profile\UsuarioRepository;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;

class PessoaContatoRepository {

    const CLASS_NAME = PessoaContato::class;
    const TABLE = 'pessoa_contato';

    use FindTrait;
    protected $conn;
    protected $model;
    protected $usuarioRepository;
    protected $pessoaFisicaRepository;
    protected $estudanteMensalidadeRepository;

    public function __construct(){
        $this->conn = Database::getInstance()->getConnection();
        $this->model = new PessoaContato();
        $this->usuarioRepository = new UsuarioRepository();
        $this->pessoaFisicaRepository = new PessoaFisicaRepository();
    }

    public function allPersons(array $params = []){

        $sql = "SELECT
            pc.*,(
                SELECT 
                    JSON_OBJECT(
                        'id', pf.id,
                        'nome', pf.nome,
                        'email', pf.email
                    )
                FROM pessoa_fisica pf
                WHERE pf.id = pc.pessoa_fisica_id
            ) AS pessoa_fisica
            FROM " . self::TABLE . " 
            pc LEFT JOIN pessoa_fisica pf ON pc.pessoa_fisica_id = pf.id
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
            $conditions[] = "pc.ativo = :ativo";
            $bindings[':ativo'] = $params['ativo'];
        }

        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY pc.created_at DESC";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute($bindings);

        return $stmt->fetchAll(\PDO::FETCH_CLASS, self::CLASS_NAME);
    }

    public function saveAll(array $data)
    {
        if (empty($data)) {
            return null;
        }

        $this->conn->beginTransaction();
        
        try {
            $userData = array_merge($data, [
                'password' => 'escola123',
                'sector' => 'responsavel_legal',
            ]);
    
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
    
            $personContactData = array_merge($data, ['person_id' => $person->id]);
            $personContact = $this->create($personContactData);

            if (is_null($personContact)) {
                $this->conn->rollBack();
                return null;
            }

            $this->conn->commit();
    
            return $personContact;
    
        } catch (\Throwable $th) {
            LoggerHelper::logInfo("Erro na transação create: {$th->getMessage()}");
            LoggerHelper::logInfo("Trace: " . $th->getTraceAsString());
            $this->conn->rollBack();
            return null;
        } finally {          
            Database::getInstance()->closeConnection();
        }
    }

    public function create(array $data) 
    {
        $existingPerson = $this->findByStudentId($data);
        if ($existingPerson) {
            return $existingPerson;
        }

        $person = $this->model->create($data);

        try{
            $stmt = $this->conn->prepare(
                "INSERT INTO " . self::TABLE . "
                    SET
                        uuid = :uuid,
                        responsavel_legal = :responsavel_legal,
                        pessoa_fisica_id = :pessoa_fisica_id
                "
            );

            $create = $stmt->execute([
                ':uuid' => $person->uuid,
                ':responsavel_legal' => $person->responsavel_legal,
                ':pessoa_fisica_id' => $person->pessoa_fisica_id
            ]);

            if(!$create){
                return null;
            }

            return $this->findByUuid($person->uuid);

        }catch(\Throwable $th){
            return null;
        } finally {          
            Database::getInstance()->closeConnection();
        }

    }

    public function updateAll(array $data){

        if(empty($data)){
            return null;
        }

        try{    
            $user = $this->usuarioRepository->update($data, $data['usuario_id']);
            if(is_null($user)){
                return null;
            }

            $person = $this->pessoaFisicaRepository->update($data, $data['pessoa_fisica_id']);
            if(is_null($person)){
                return null;
            }
            
            $personContact = $this->update($data, $data['id']);

            if(is_null($personContact)){
                return null;
            }

            return $personContact;

        } catch(\Throwable $th) {
            return null;
        } finally {          
            Database::getInstance()->closeConnection();
        }
    }

    public function update(array $data, int $id)
    {
        $person_contact = $this->findById($id);

        $person_contact = $this->model->update($data, $person_contact);

        try{
            $stmt = $this->conn->prepare(
                "UPDATE " . self::TABLE . "
                    set
                        responsavel_legal = :responsavel_legal,
                        pessoa_fisica_id = :pessoa_fisica_id
                        ativo = :ativo,
                    WHERE id = :id
                    "
            );

            $updated = $stmt->execute([
                ':responsavel_legal' => $person_contact->responsavel_legal,
                ':pessoa_fisica_id' => $person_contact->pessoa_fisica_id,
                ':ativo' => $person_contact->ativo,
                ':id' => $id
            ]);

            if(!$updated){
                return null;
            }

            return $this->findById($id);
        }catch(\Throwable $th){
            return null;
        } finally {          
            Database::getInstance()->closeConnection();
        }
    }

    public function deleteAll($person_contact_id)
    {
        if (is_null($person_contact_id)) {
            return null;
        }

        $pessoa_fisica = $this->findById($person_contact_id);

        $this->usuarioRepository->delete($pessoa_fisica->usuario_id);

        $this->pessoaFisicaRepository->delete($pessoa_fisica->id);

        return $this->delete($person_contact_id);
    }

    public function delete(int $id){
        $stmt = $this->conn->prepare(
            "UPDATE " . self::TABLE . "
                SET
                    ativo = 0
                WHERE id = :id
            "
        );

            $updated = $stmt->execute(['id' => $id]);

            return $updated;
    }

    public function findByStudentId(array $criteria): ?array
    {
        try {
            $conditions = [];
            $params = [];

            if (!empty($criteria['person_id'])) {
                $conditions[] = "pessoa_fisica_id = :pessoa_id";
                $params[':pessoa_id'] = $criteria['person_id'];
            }

            if (empty($conditions)) {
                return null; 
            }

            $sql = "SELECT * FROM " . self::TABLE . " WHERE " . implode(' AND ', $conditions);
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);

            $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::CLASS_NAME);
            $result = $stmt->fetch();  

            return $result ?: null; 
        } catch (\Throwable $th) {
            return null;
        } finally {          
            Database::getInstance()->closeConnection();
        }
    }
}