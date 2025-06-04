<?php 

namespace App\Repositories\Student;

use App\Config\Database;
use App\Interfaces\Student\IEstudanteRepository;
use App\Models\Student\Estudante;
use App\Repositories\Person\PessoaContatoRepository;
use App\Repositories\Student\EstudanteMensalidadeRepository;
use App\Repositories\MonthlyFees\MensalidadeRepository;
use App\Repositories\Person\PessoaFisicaRepository;
use App\Repositories\Student\EstudanteTurmaRepository;
use App\Repositories\Profile\UsuarioRepository;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;

class EstudanteRepository implements IEstudanteRepository {

    const CLASS_NAME = Estudante::class;
    const TABLE = 'estudantes';

    use FindTrait;
    protected $conn;
    protected $model;
    protected $usuarioRepository;
    protected $pessoaFisicaRepository;
    protected $pessoaContatoRepository;
    protected $estudanteMensalidadeRepository;
    protected $estudanteTurmaRepository;
    protected $mensalidadeRepository;
    

    public function __construct(){
        $this->conn = Database::getInstance()->getConnection();
        $this->model = new Estudante();
        $this->usuarioRepository = new UsuarioRepository();
        $this->pessoaFisicaRepository = new PessoaFisicaRepository();
        $this->estudanteTurmaRepository = new EstudanteTurmaRepository();
        $this->pessoaContatoRepository = new PessoaContatoRepository();
        $this->estudanteMensalidadeRepository = new EstudanteMensalidadeRepository();
        $this->mensalidadeRepository = new MensalidadeRepository();
    }

    public function allStudents(array $params = []){

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
        ";

        $conditions = [];
        $bindings = [];

        if (isset($params['name_email'])) {
            $conditions[] = "(pf.nome LIKE :name_email OR pf.email LIKE :name_email)";
            $bindings[':name_email'] = "%" . $params['name_email'] . "%";
        }

        if (isset($params['contact_person_id'])) {
            $conditions[] = "e.pessoa_contato_id = :pessoa_contato_id";
            $bindings[':pessoa_contato_id'] = $params['contact_person_id'];
        }

        if (isset($params['id'])) {
            $conditions[] = "pf.id = :id";
            $bindings[':id'] = $params['id'];
        }

        if (isset($params['email'])) {
            $conditions[] = "pf.email = :email";
            $bindings[':email'] = $params['email'];
        }

        if (isset($params['situation']) && !empty($params['situation'])) {
            $conditions[] = "e.ativo = :situation";
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

    public function studentWithPersonByUuid(string $uuid)
    {
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
    }

    public function saveAll(array $data)
    {
        if (empty($data)) {
            return null;
        }
        
        try {
            $userData = array_merge($data, [
                'password' => 'escola123',
                'sector' => 'estudante',
            ]);
            
            $user = $this->usuarioRepository->create($userData);
    
            $personData = array_merge($data, ['usuario_id' => $user->id]);
            $person = $this->pessoaFisicaRepository->create($personData);
            
            $studentData = array_merge($data, ['person_id' => $person->id]);
            $student = $this->create($studentData);
            
            if(isset($data['procees_monthylees']) && $data['procees_monthylees'] == 'sim') {
                $monthlyData = array_merge($data, ['student_id' => $student->id]);
                $monthly = $this->estudanteMensalidadeRepository->create($monthlyData);
            }
            
            return $student;
    
        } catch (\Throwable $th) {
            LoggerHelper::logInfo("Erro na transação create: {$th->getMessage()}");
            LoggerHelper::logInfo("Trace: " . $th->getTraceAsString());
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

        $estudante = $this->model->create($data);

        try{
            $stmt = $this->conn->prepare(
                "INSERT INTO " . self::TABLE . "
                    SET
                        uuid = :uuid,
                        matricula = :matricula,
                        pessoa_fisica_id = :pessoa_fisica_id,
                        pessoa_contato_id = :pessoa_contato_id
                "
            );

            $create = $stmt->execute([
                ':uuid' => $estudante->uuid,
                ':matricula' => $estudante->matricula,
                ':pessoa_fisica_id' => $estudante->pessoa_fisica_id,
                ':pessoa_contato_id' => $estudante->pessoa_contato_id
            ]);

            if(!$create){
                return null;
            }

            return $this->findByUuid($estudante->uuid);

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
            
            $estudante = $this->update($data, (int)$data['id']);

            if(is_null($estudante)){
                return null;
            }

            if(isset($data['procees_monthylees']) && $data['procees_monthylees'] == 'sim') {
                $monthlyData = $this->estudanteMensalidadeRepository
                ->getMonthlyFee(
                [
                   'student_id' => $estudante->id, 
                   'active' => 1
                ]
                );

                $monthly = $this->estudanteMensalidadeRepository->update($data, $monthlyData->id);

                if(is_null($monthly)){
                    return null;
                }
            }

            return $estudante;

        } catch(\Throwable $th) {
            return null;
        } finally {          
            Database::getInstance()->closeConnection();
        }
    }

    public function update(array $data, int $id)
    {
        $estudante = $this->findById($id);

        $estudante = $this->model->update($data, $estudante);

        try{
            $stmt = $this->conn->prepare(
                "UPDATE " . self::TABLE . "
                    set
                        matricula = :matricula,
                        pessoa_fisica_id = :pessoa_fisica_id,
                        ativo = :ativo,
                        pessoa_contato_id = :pessoa_contato_id
                    WHERE id = :id
                    "
            );

            $updated = $stmt->execute([
                ':matricula' => $estudante->matricula,
                ':pessoa_fisica_id' => $estudante->pessoa_fisica_id,
                ':ativo' => $estudante->ativo,
                ':pessoa_contato_id' => $estudante->pessoa_contato_id,
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

    public function deleteAll($estudante){
        $pessoa_fisica = $this->pessoaFisicaRepository->findById($estudante->pessoa_fisica_id);

        $this->usuarioRepository->delete($pessoa_fisica->usuario_id);

        $this->pessoaFisicaRepository->delete($pessoa_fisica->id);

        $this->pessoaContatoRepository->deleteAll($estudante->pessoa_contato_id);

        return $this->delete($estudante->id);
    }

    public function removeAll($id){
        $estudante = $this->findById((int)$id);

        if (is_null($estudante)) {
            return null;
        }
        
        $estudanteMensalidade = $this->estudanteMensalidadeRepository->allMonthlyfees(['student_id' => $id]);
        $estudanteTurma = $this->estudanteTurmaRepository->allClassStudents(['student_id' => $id]);
        $mensalidade = $this->mensalidadeRepository->allMonthlyfees(['student_monthlyfees_id' => $estudanteMensalidade[0]->id]);
  
        $this->mensalidadeRepository->remove((int)$mensalidade[0]->id);
        $this->estudanteMensalidadeRepository->remove((int)$estudanteMensalidade[0]->id);
        $this->estudanteTurmaRepository->remove((int)$estudanteTurma[0]->id);
        $this->remove((int)$id);

        $pessoa_fisica = $this->pessoaFisicaRepository->findById((int)$estudante->pessoa_fisica_id);
    
        if (is_null($pessoa_fisica)) {
            return null;
        }

        $removedPessoaFisica = $this->pessoaFisicaRepository->remove((int)$pessoa_fisica->id);

        return $this->usuarioRepository->remove((int)$pessoa_fisica->usuario_id);
    }

    public function remove($id) :?bool 
    {
        $estudante = $this->findById((int)$id);

        if (is_null($estudante)) {
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

    private function verifyAndRollback($object, string $errorMessage)
    {
        if (is_null($object->id ?? null)) {
            throw new \Exception($errorMessage);
        }
    }

    public function findByStudentId(array $criteria): ?Estudante
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

    public function studentByPersonId(int $person_id){

        $sql = "SELECT
            e.*
            FROM " . self::TABLE . " e
            WHERE e.pessoa_fisica_id = :id
        ";

        $sql .= " ORDER BY e.created_at DESC LIMIT 1";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([':id' => $person_id]);

        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::CLASS_NAME);
        return $stmt->fetch();  
    }
}