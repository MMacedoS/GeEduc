<?php

namespace App\Repositories\Profile;

use App\Config\Database;
use App\Config\SingletonInstance;
use App\Interfaces\Profile\IUsuarioRepository;
use App\Models\Profile\Usuario;
use App\Repositories\File\ArquivoRepository;
use App\Repositories\Permission\PermissaoRepository;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;
use PDO;

class UsuarioRepository extends SingletonInstance implements IUsuarioRepository
{
    const CLASS_NAME = Usuario::class;
    const TABLE = 'usuarios';

    use FindTrait;

    private $permissioRepository;
    protected $arquivoRepository;

    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
        $this->model = new Usuario();
        $this->permissioRepository = PermissaoRepository::getInstance();
        $this->arquivoRepository = ArquivoRepository::getInstance();
    }

    public function all(array $params = [])
    {
        $sql = "SELECT * FROM " . self::TABLE;

        $conditions = [];
        $bindings = [];

        if (isset($params['name_email'])) {
            $conditions[] = "(nome LIKE :name_email or email LIKE :name_email)";
            $bindings[':name_email'] = '%' . $params['name_email'] . '%';
        }

        if (isset($params['access']) && $params['access'] != '') {
            $conditions[] = "painel = :access";
            $bindings[':access'] = $params['access'];
        }

        if (isset($params['situation']) && $params['situation'] != '') {
            $conditions[] = "ativo = :situation";
            $bindings[':situation'] = $params['situation'];
        }

        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY nome ASC";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute($bindings);

        return $stmt->fetchAll(\PDO::FETCH_CLASS, self::CLASS_NAME);
    }

    public function create(array $data, bool $forceNewPassword = true)
    {
        $existingUser = $this->findByEmailAndSector($data['email'], $data['sector']);
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
            "
                );
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

    public function findByEmail(string $email)
    {
        try {
            $stmt = $this->conn->prepare(
                "SELECT * FROM " . self::TABLE . " WHERE email = :email LIMIT 1"
            );
            $stmt->execute([':email' => $email]);
            $stmt->setFetchMode(\PDO::FETCH_CLASS, self::CLASS_NAME);

            return $stmt->fetch() ?: null;
        } catch (\Throwable $th) {
            LoggerHelper::logInfo($th->getMessage());
            return null;
        }
    }

    public function findByEmailAndSector(string $email, string $sector)
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
        isset($data['password']) ? $senha = (string)$data['password'] : $senha = $existingUser->senha;
        $user = $this->model
            ->update(
                $data,
                $existingUser,
                !hash_equals(
                    $senha,
                    $existingUser->senha
                )
            );

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

    public function updatePassword(array $data, int $id)
    {
        $existingUser = $this->findById($id);
        if (!$existingUser) {
            return null;
        }

        if (!password_verify($data['password_old'], $existingUser->senha)) {
            return null;
        }

        return $this->update($data, (int)$existingUser->id);
    }

    public function getLogin(string $email, string $senha)
    {
        if (empty($email) || empty($senha)) {
            return null;
        }

        $stmt = $this->conn->prepare(
            "SELECT id as code, senha, nome, email, painel, ativo, arquivo_id, uuid as id 
             FROM " . self::TABLE . " 
             WHERE email = :email and ativo=1"
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

        $updated = $stmt->execute([':id' => $id]);

        return $updated;
    }

    public function remove($id): ?bool
    {

        $usuario = $this->findById((int)$id);

        if (is_null($usuario)) {
            return null;
        }

        if (!$this->removePermissions($id)) {
            return null;
        };

        try {
            $stmt = $this->conn->prepare("DELETE FROM " . self::TABLE . " WHERE id = :id");
            $delete = $stmt->execute([
                ':id' => $id
            ]);
            if ($delete) {
                return true;
            }
            return false;
        } catch (\Throwable $th) {
            LoggerHelper::logInfo("Erro na transação delete: {$th->getMessage()}");
            LoggerHelper::logInfo("Trace: " . $th->getTraceAsString());
            return null;
        }
    }

    public function findPermissions(int $usuario_id)
    {
        $stmt = $this->conn
            ->prepare(
                "SELECT permissao_as_usuario.* 
                FROM permissao_as_usuario 
                where usuario_id = :usuario_id"
            );
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
        $access = !is_null($userFromDb->painel) ? $userFromDb->painel : null;

        $permissions = $this->permissionList($access);

        if (is_null($permissions)) {
            return $userFromDb;
        }

        $permissionNames = array_map(fn($permission) => $permission['name'], $permissions);

        $placeholders = implode(',', array_fill(0, count($permissionNames), '?'));

        $stmt = $this->conn->prepare(
            "SELECT id FROM permissao WHERE name IN ($placeholders)"
        );

        $stmt->execute($permissionNames);

        $permissionIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $this->addPermissions(['permissions' => $permissionIds], $userFromDb->id);

        return $userFromDb;
    }

    public function updatePhoto($file, $dir, $id_user)
    {
        $file = $this->arquivoRepository->create($file, $dir);

        $stmt = $this->conn
            ->prepare(
                "UPDATE " . self::TABLE . " 
             SET arquivo_id = :file_id 
             WHERE id = :id"
            );

        $updated = $stmt->execute([':id' => $id_user, ':file_id' => $file->id]);

        return $file;
    }

    private function permissionList($sector)
    {
        if ($sector == 'administrativo') {
            $permissao = array(
                array('id' => '1', 'name' => 'visualizar_usuarios', 'description' => 'Visualizar todos os usuarios'),
                array('id' => '2', 'name' => 'editar_usuario', 'description' => 'atualizar dados do usuários'),
                array('id' => '3', 'name' => 'cadastrar_usuario', 'description' => 'cadastrar usuários'),
                array('id' => '4', 'name' => 'deletar_usuario', 'description' => 'excluir conta de usuários'),
                array('id' => '5', 'name' => 'visualizar_cadastro', 'description' => 'acesso ao cadastros '),
                array('id' => '6', 'name' => 'visualizar_turmas', 'description' => 'visualizar turmas geral'),
                array('id' => '7', 'name' => 'visualizar_professores', 'description' => 'visualizar_professores'),
                array('id' => '8', 'name' => 'visualizar_estudantes', 'description' => 'visualizar_estudantes'),
                array('id' => '9', 'name' => 'visualizar_disciplinas', 'description' => 'visualizar_disciplinas'),
                array('id' => '10', 'name' => 'visualizar_planos', 'description' => 'visualizar_planos'),
                array('id' => '11', 'name' => 'visualizar_turmas_estudantes', 'description' => 'visualizar turma estudantes'),
                array('id' => '12', 'name' => 'visualizar_mensalidades', 'description' => 'visualizar_mensalidades'),
                array('id' => '13', 'name' => 'deletar_professor', 'description' => 'deletar professore'),
                array('id' => '14', 'name' => 'deletar_estudante', 'description' => 'deletar apartamento'),
                array('id' => '15', 'name' => 'editar_professor', 'description' => 'editar apartamento'),
                array('id' => '16', 'name' => 'editar_estudante', 'description' => 'editar cliente'),
                array('id' => '17', 'name' => 'cadastrar_professor', 'description' => 'cadastrar cliente'),
                array('id' => '18', 'name' => 'cadastrar_estudante', 'description' => 'cadastrar apartamento'),
                array('id' => '19', 'name' => 'cadastrar_turma', 'description' => 'cadastrar dados de reservas'),
                array('id' => '20', 'name' => 'editar_turma', 'description' => 'editar dados de reservas'),
                array('id' => '21', 'name' => 'deletar_turma', 'description' => 'deleção de dados das reservas'),
                array('id' => '22', 'name' => 'editar_disciplina', 'description' => 'editar produto'),
                array('id' => '23', 'name' => 'visualizar_conteudos', 'description' => 'visualizar produtos'),
                array('id' => '24', 'name' => 'deletar_conteudos', 'description' => 'deletar produtos'),
                array('id' => '25', 'name' => 'cadastrar_conteudos', 'description' => 'cadastrar produtos'),
                array('id' => '26', 'name' => 'cadastrar_planos', 'description' => 'cadastrar vendas'),
                array('id' => '27', 'name' => 'visualizar_pedagogico', 'description' => 'visualizar as ações para menu pedagogicos'),
                array('id' => '28', 'name' => 'visualizar_financeiro', 'description' => 'visualizar ações do bloco financeiro'),
                array('id' => '29', 'name' => 'editar_plano', 'description' => 'editar os planos'),
                array('id' => '30', 'name' => 'deletar_plano', 'description' => 'deletar planos de mensalidade'),
                array('id' => '31', 'name' => 'visualizar_contas_bancarias', 'description' => 'acesso visualizar contas bancarias '),
                array('id' => '32', 'name' => 'cadastrar_conta', 'description' => ''),
                array('id' => '33', 'name' => 'editar_conta', 'description' => ''),
                array('id' => '34', 'name' => 'deletar_conta', 'description' => ''),
                array('id' => '37', 'name' => 'visualizar_turma_estudante', 'description' => 'visualizar_turma_estudante'),
                array('id' => '38', 'name' => 'vincular_turmas_estudantes', 'description' => 'vincular_turmas_estudantes'),
                array('id' => '39', 'name' => 'editar_turmas_estudantes', 'description' => 'editar_turmas_estudantes'),
                array('id' => '40', 'name' => 'cadastrar_turmas_estudantes', 'description' => 'cadastrar_turmas_estudantes'),
                array('id' => '41', 'name' => 'inativar_vinculos', 'description' => 'inativar_vinculos'),
                array('id' => '79', 'name' => 'cadastrar_mensalidade', 'description' => 'cadastrar_mensalidade'),
                array('id' => '123', 'name' => 'editar_mensalidade', 'description' => 'edição dos dados da mensalidade'),
                array('id' => '124', 'name' => 'cancelar_mensalidade', 'description' => 'alterar a situação da mensalidade para cancelado'),
                array('id' => '125', 'name' => 'efetivar_mensalidade', 'description' => 'alterar o status da mensalidade para pago'),
                array('id' => '126', 'name' => 'visualizar_cards_dashboard', 'description' => 'visualizar_cards_dashboard'),
                array('id' => '127', 'name' => 'editar_turmas_disciplinas', 'description' => 'editar_turmas_disciplinas'),
                array('id' => '128', 'name' => 'deletar_turmas_disciplinas', 'description' => 'deletar_turmas_disciplinas'),
                array('id' => '129', 'name' => 'vincular_turmas_disciplinas', 'description' => 'vincular_turmas_disciplinas'),
                array('id' => '130', 'name' => 'visualizar_atividades', 'description' => 'visualizar_atividades'),
                array('id' => '131', 'name' => 'cadastrar_atividade', 'description' => 'cadastrar_atividade'),
                array('id' => '132', 'name' => 'cadastrar_coordenador', 'description' => 'cadastrar_coordenador'),
                array('id' => '133', 'name' => 'editar_coordenador', 'description' => 'editar_coordenador'),
                array('id' => '134', 'name' => 'visualizar_coordenadores', 'description' => 'visualizar_coordenadores'),
                array('id' => '135', 'name' => 'deletar_coordenador', 'description' => 'deletar_coordenador'),
                array('id' => '136', 'name' => 'visualizar_bimestres', 'description' => 'visualizar_bimestres'),
                array('id' => '137', 'name' => 'cadastrar_pessoa', 'description' => 'cadastrar_pessoa'),
                array('id' => '138', 'name' => 'editar_pessoa', 'description' => 'editar_pessoa'),
                array('id' => '139', 'name' => 'deletar_pessoa', 'description' => 'deletar_pessoa'),
                array('id' => '140', 'name' => 'visualizar_pessoas', 'description' => 'visualizar_pessoas'),
                array('id' => '142', 'name' => 'visualizar_carga_horaria', 'description' => 'visualizar cargas horarias'),
                array('id' => '143', 'name' => 'visualizar_periodos', 'description' => 'visualizar_periodos'),
                array('id' => '144', 'name' => 'editar_periodo', 'description' => 'editar periodos'),
                array('id' => '145', 'name' => 'cadastrar_periodo', 'description' => 'cadastrar_periodo'),
                array('id' => '146', 'name' => 'deletar_periodo', 'description' => 'deletar_periodo'),
                array('name' => 'cadastrar_aula', 'description' => 'cadastrar aulas '),
                array('name' => 'editar_aula', 'description' => 'editar aulas '),
                array('name' => 'cadastrar_permissao', 'description' => 'cadastrar permissao'),
                array('name' => 'deletar_aula', 'description' => 'deletar aulas '),
                array('name' => 'visualizar_aulas', 'description' => 'visualizar aulas'),
                array('name' => 'editar_turmas_disciplinas', 'description' => 'Coordenar turmas')
            );
            return $permissao;
        }

        if ($sector == 'coordenador') {
            $permissao = array(
                array('id' => '5', 'name' => 'visualizar_cadastro', 'description' => 'acesso ao cadastros '),
                array('id' => '6', 'name' => 'visualizar_turmas', 'description' => 'visualizar turmas geral'),
                array('id' => '7', 'name' => 'visualizar_professores', 'description' => 'visualizar_professores'),
                array('id' => '8', 'name' => 'visualizar_estudantes', 'description' => 'visualizar_estudantes'),
                array('id' => '9', 'name' => 'visualizar_disciplinas', 'description' => 'visualizar_disciplinas'),
                array('id' => '10', 'name' => 'visualizar_planos', 'description' => 'visualizar_planos'),
                array('id' => '11', 'name' => 'visualizar_turmas_estudantes', 'description' => 'visualizar turma estudantes'),
                array('id' => '12', 'name' => 'visualizar_mensalidades', 'description' => 'visualizar_mensalidades'),
                array('id' => '13', 'name' => 'deletar_professor', 'description' => 'deletar professore'),
                array('id' => '14', 'name' => 'deletar_estudante', 'description' => 'deletar apartamento'),
                array('id' => '15', 'name' => 'editar_professor', 'description' => 'editar apartamento'),
                array('id' => '16', 'name' => 'editar_estudante', 'description' => 'editar cliente'),
                array('id' => '17', 'name' => 'cadastrar_professor', 'description' => 'cadastrar cliente'),
                array('id' => '18', 'name' => 'cadastrar_estudante', 'description' => 'cadastrar apartamento'),
                array('id' => '19', 'name' => 'cadastrar_turma', 'description' => 'cadastrar dados de reservas'),
                array('id' => '20', 'name' => 'editar_turma', 'description' => 'editar dados de reservas'),
                array('id' => '21', 'name' => 'deletar_turma', 'description' => 'deleção de dados das reservas'),
                array('id' => '22', 'name' => 'editar_disciplina', 'description' => 'editar produto'),
                array('id' => '23', 'name' => 'visualizar_conteudos', 'description' => 'visualizar produtos'),
                array('id' => '24', 'name' => 'deletar_conteudos', 'description' => 'deletar produtos'),
                array('id' => '25', 'name' => 'cadastrar_conteudos', 'description' => 'cadastrar produtos'),
                array('id' => '26', 'name' => 'cadastrar_planos', 'description' => 'cadastrar vendas'),
                array('id' => '27', 'name' => 'visualizar_pedagogico', 'description' => 'visualizar as ações para menu pedagogicos'),
                array('id' => '29', 'name' => 'editar_plano', 'description' => 'editar os planos'),
                array('id' => '37', 'name' => 'visualizar_turma_estudante', 'description' => 'visualizar_turma_estudante'),
                array('id' => '38', 'name' => 'vincular_turmas_estudantes', 'description' => 'vincular_turmas_estudantes'),
                array('id' => '39', 'name' => 'editar_turmas_estudantes', 'description' => 'editar_turmas_estudantes'),
                array('id' => '40', 'name' => 'cadastrar_turmas_estudantes', 'description' => 'cadastrar_turmas_estudantes'),
                array('id' => '41', 'name' => 'inativar_vinculos', 'description' => 'inativar_vinculos'),
                array('id' => '126', 'name' => 'visualizar_cards_dashboard', 'description' => 'visualizar_cards_dashboard'),
                array('id' => '127', 'name' => 'editar_turmas_disciplinas', 'description' => 'editar_turmas_disciplinas'),
                array('id' => '128', 'name' => 'deletar_turmas_disciplinas', 'description' => 'deletar_turmas_disciplinas'),
                array('id' => '129', 'name' => 'vincular_turmas_disciplinas', 'description' => 'vincular_turmas_disciplinas'),
                array('id' => '130', 'name' => 'visualizar_atividades', 'description' => 'visualizar_atividades'),
                array('id' => '131', 'name' => 'cadastrar_atividade', 'description' => 'cadastrar_atividade'),
                array('id' => '132', 'name' => 'cadastrar_coordenador', 'description' => 'cadastrar_coordenador'),
                array('id' => '133', 'name' => 'editar_coordenador', 'description' => 'editar_coordenador'),
                array('id' => '134', 'name' => 'visualizar_coordenadores', 'description' => 'visualizar_coordenadores'),
                array('id' => '135', 'name' => 'deletar_coordenador', 'description' => 'deletar_coordenador'),
                array('id' => '136', 'name' => 'visualizar_bimestres', 'description' => 'visualizar_bimestres'),
                array('id' => '137', 'name' => 'cadastrar_pessoa', 'description' => 'cadastrar_pessoa'),
                array('id' => '138', 'name' => 'editar_pessoa', 'description' => 'editar_pessoa'),
                array('id' => '139', 'name' => 'deletar_pessoa', 'description' => 'deletar_pessoa'),
                array('id' => '140', 'name' => 'visualizar_pessoas', 'description' => 'visualizar_pessoas'),
                array('id' => '142', 'name' => 'visualizar_carga_horaria', 'description' => 'visualizar cargas horarias'),
                array('id' => '143', 'name' => 'visualizar_periodos', 'description' => 'visualizar_periodos'),
                array('id' => '144', 'name' => 'editar_periodo', 'description' => 'editar periodos'),
                array('id' => '145', 'name' => 'cadastrar_periodo', 'description' => 'cadastrar_periodo'),
                array('id' => '146', 'name' => 'deletar_periodo', 'description' => 'deletar_periodo'),
                array('id' => '3', 'name' => 'cadastrar_usuario', 'description' => 'cadastrar usuários'),
                array('name' => 'coordenador', 'description' => 'Coordenar turmas'),
                array('name' => 'editar_turmas_disciplinas', 'description' => 'Coordenar turmas')
            );
            return $permissao;
        }

        if ($sector == 'professor') {
            $permissao = array(
                array('id' => '36', 'name' => 'professor', 'description' => 'permissao para acesso professor')
            );
            return $permissao;
        }

        if ($sector == 'estudante') {
            $permissao = array(
                array('id' => '141', 'name' => 'estudante', 'description' => 'estudantes estudantes')
            );
            return $permissao;
        }

        if ($sector == 'responsavel_legal') {
            $permissao = array(
                array('id' => '141', 'name' => 'responsavel_legal', 'description' => 'responsavel legal dos estudantes')
            );
            return $permissao;
        }
    }
}
