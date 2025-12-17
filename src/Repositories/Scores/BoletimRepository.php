<?php

namespace App\Repositories\Scores;

use App\Config\Database;
use App\Config\SingletonInstance;
use App\Interfaces\Scores\IBoletimRepository;
use App\Models\Scores\Nota;
use App\Repositories\Activitie\AtividadeRepository;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;
use PDO;

class BoletimRepository extends SingletonInstance implements IBoletimRepository
{
    const CLASS_NAME = Nota::class;
    const TABLE = 'notas';

    use FindTrait;

    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
        $this->model = new Nota();
    }

    public function allSumScoresByParams(array $params = [])
    {
        $sql = "SELECT 
            n.estudante_turma_id,
            SUM(n.nota) AS media
        FROM notas n
        LEFT JOIN atividade a ON n.atividade_id = a.id
        LEFT JOIN turma_disciplina td ON td.id = a.turma_disciplina_id
        LEFT JOIN estudante_turma et ON et.id = n.estudante_turma_id";

        $conditions = [];
        $bindings = [];

        if (isset($params['class_discipline_id'])) {
            $conditions[] = 'a.turma_disciplina_id = :class_discipline_id';
            $bindings[':class_discipline_id'] = $params['class_discipline_id'];
        }

        if (isset($params['student_class_id'])) {
            $conditions[] = 'n.estudante_turma_id = :student_class_id';
            $bindings[':student_class_id'] = $params['student_class_id'];
        }

        if (isset($params['period_id'])) {
            $conditions[] = 'n.periodo_id = :period_id';
            $bindings[':period_id'] = $params['period_id'];
        }

        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= "
            GROUP BY n.estudante_turma_id 
            ORDER BY n.periodo_id ASC";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($bindings);

            return $stmt->fetchAll(PDO::FETCH_CLASS);
        } catch (\PDOException $e) {
            throw new \Exception("Database query error: " . $e->getMessage());
        }
    }

    public function allScoresByStudents(array $params = [])
    {
        $sql = "SELECT 
                    d.nome AS disciplina,
                    b.periodo AS periodo,
                    GROUP_CONCAT(CONCAT(a.tipo, ': ', n.nota) SEPARATOR '</br> ') AS atividades_notas,
                    pf.nome as professor
                FROM 
                    notas n
                LEFT JOIN 
                    estudante_turma et ON n.estudante_turma_id = et.id
                LEFT JOIN 
                    atividade a ON n.atividade_id = a.id
                LEFT JOIN 
                    turma_disciplina td ON a.turma_disciplina_id = td.id
                LEFT JOIN 
                    professor_disciplina pd ON td.professor_disciplina_id = pd.id
                LEFT JOIN 
                    professores p ON pd.professor_id = p.id
                LEFT JOIN 
                    pessoa_fisica pf ON p.pessoa_fisica_id = pf.id
                LEFT JOIN 
                    disciplinas d ON pd.disciplina_id = d.id
                LEFT JOIN 
                    periodo b ON n.periodo_id = b.id                
            ";

        $conditions = [];
        $bindings = [];

        if (isset($params['student_class_id'])) {
            $conditions[] = 'n.estudante_turma_id = :student_class_id';
            $bindings[':student_class_id'] = $params['student_class_id'];
        }

        if (isset($params['period_id'])) {
            $conditions[] = 'n.periodo_id = :period_id';
            $bindings[':period_id'] = $params['period_id'];
        }

        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " GROUP BY 
                    pf.nome, et.id, d.id, b.id
                ORDER BY 
                 d.nome, b.periodo;";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($bindings);

            return $stmt->fetchAll(PDO::FETCH_CLASS);
        } catch (\PDOException $e) {
            throw new \Exception("Database query error: " . $e->getMessage());
        }
    }

    public function totalScoreByStudentsAndDisciplines(array $params = [])
    {
        $sql = "SELECT 
                sum(n.nota) as total, 
                d.id, 
                d.nome 
            FROM `notas` n 
            LEFT JOIN atividade a 
            on a.id = n.atividade_id 
            LEFT JOIN turma_disciplina td 
            on td.id = a.turma_disciplina_id
            LEFT JOIN professor_disciplina pd 
            on pd.id = td.professor_disciplina_id 
            LEFT JOIN disciplinas d 
            on d.id = pd.disciplina_id";

        $conditions = [];
        $bindings = [];

        if (isset($params['student_class_id'])) {
            $conditions[] = 'n.estudante_turma_id = :student_class_id';
            $bindings[':student_class_id'] = $params['student_class_id'];
        }

        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " GROUP BY 
                    d.id, d.nome
                ORDER BY 
                 d.nome;";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($bindings);

            return $stmt->fetchAll(PDO::FETCH_CLASS);
        } catch (\PDOException $e) {
            throw new \Exception("Database query error: " . $e->getMessage());
        }
    }

    public function allRecuperationScoreByParams(array $params = [])
    {
        $sql = "SELECT 
            r.estudante_turma_id,
            r.nota
        FROM recuperacao r 
        LEFT JOIN turma_disciplina td ON td.id = r.turma_disciplina_id
        LEFT JOIN estudante_turma et ON et.id = r.estudante_turma_id";

        $conditions = [];
        $bindings = [];

        if (isset($params['class_discipline_id'])) {
            $conditions[] = 'r.turma_disciplina_id = :class_discipline_id';
            $bindings[':class_discipline_id'] = $params['class_discipline_id'];
        }

        if (isset($params['student_class_id'])) {
            $conditions[] = 'r.estudante_turma_id = :student_class_id';
            $bindings[':student_class_id'] = $params['student_class_id'];
        }

        if (isset($params['period'])) {
            $conditions[] = 'r.periodo = :period';
            $bindings[':period'] = $params['period'];
        }

        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= "
            ORDER BY r.id DESC";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($bindings);

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \Exception("Database query error: " . $e->getMessage());
        }
    }

    public function scoreFinalByStudentAndDisciplineAndPeriod(array $params = [])
    {
        $sql = "SELECT 
            nf.estudante_turma_id,
            nf.nota
        FROM nota_final nf 
        LEFT JOIN turma_disciplina td ON td.id = nf.turma_disciplina_id
        LEFT JOIN estudante_turma et ON et.id = nf.estudante_turma_id";

        $conditions = [];
        $bindings = [];

        if (isset($params['class_discipline_id'])) {
            $conditions[] = 'nf.turma_disciplina_id = :class_discipline_id';
            $bindings[':class_discipline_id'] = $params['class_discipline_id'];
        }

        if (isset($params['student_class_id'])) {
            $conditions[] = 'nf.estudante_turma_id = :student_class_id';
            $bindings[':student_class_id'] = $params['student_class_id'];
        }

        if (isset($params['ano_letivo'])) {
            $conditions[] = 'nf.ano_letivo = :ano_letivo';
            $bindings[':ano_letivo'] = $params['ano_letivo'];
        }

        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= "
            ORDER BY nf.id DESC";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($bindings);

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return null;
        }
    }

    public function allcoreByStudentsAndActiviteAndPeriod(array $params = [])
    {
        $sql = "SELECT 
                n.nota, 
                d.id, 
                d.nome 
            FROM `notas` n 
            LEFT JOIN atividade a 
            on a.id = n.atividade_id 
            LEFT JOIN turma_disciplina td 
            on td.id = a.turma_disciplina_id
            LEFT JOIN professor_disciplina pd 
            on pd.id = td.professor_disciplina_id 
            LEFT JOIN disciplinas d 
            on d.id = pd.disciplina_id";

        $conditions = [];
        $bindings = [];

        if (isset($params['student_class_id'])) {
            $conditions[] = 'n.estudante_turma_id = :student_class_id';
            $bindings[':student_class_id'] = $params['student_class_id'];
        }

        if (isset($params['activitie_id'])) {
            $conditions[] = 'n.atividade_id= :activitie_id';
            $bindings[':activitie_id'] = $params['activitie_id'];
        }

        if (isset($params['period'])) {
            $conditions[] = 'n.periodo_id= :period';
            $bindings[':period'] = $params['period'];
        }

        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " GROUP BY 
                    d.id, d.nome
                ORDER BY 
                 d.nome;";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($bindings);

            return $stmt->fetchAll(PDO::FETCH_CLASS);
        } catch (\PDOException $e) {
            throw new \Exception("Database query error: " . $e->getMessage());
        }
    }

    public function scoreByStudentsAndActiviteAndPeriod(array $params = [])
    {
        $sql = "SELECT 
            n.estudante_turma_id,
            n.nota
        FROM notas n 
        LEFT JOIN atividade a ON a.id = n.atividade_id
        LEFT JOIN estudante_turma et ON et.id = n.estudante_turma_id";

        $conditions = [];
        $bindings = [];

        if (isset($params['activitie_id'])) {
            $conditions[] = 'n.atividade_id = :activitie_id';
            $bindings[':activitie_id'] = $params['activitie_id'];
        }

        if (isset($params['student_class_id'])) {
            $conditions[] = 'n.estudante_turma_id = :student_class_id';
            $bindings[':student_class_id'] = $params['student_class_id'];
        }

        if (isset($params['period'])) {
            $conditions[] = 'n.periodo_id = :period';
            $bindings[':period'] = $params['period'];
        }

        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= "
            ORDER BY n.id DESC";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($bindings);

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \Exception("Database query error: " . $e->getMessage());
        }
    }
}
