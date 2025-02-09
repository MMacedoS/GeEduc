<?php

namespace App\Repositories\Contracts;

use App\Config\Database;
use App\Interfaces\Contracts\IContratoRepository;
use App\Models\Contracts\Contrato;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;

class ContratoRepository implements IContratoRepository {
    const CLASS_NAME = Contrato::class;
    const TABLE = 'contratos';

    use FindTrait;
    protected $conn;
    protected $model;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
        $this->model = new Contrato();
    }

    public function create(array $data)
    {
        $class = $this->model->create($data);
        try {
            $stmt = $this->conn->prepare(
                "INSERT INTO " . self::TABLE . " 
                SET 
                    uuid = :uuid,
                    estudante_id = :estudante_id,
                    ano_letivo = :ano_letivo,
                    public_id = :public_id,
                    quantidade_assinaturas = :quantidade_assinaturas
                "
            );

            $create = $stmt->execute([
                ':uuid' => $class->uuid,
                ':estudante_id' => $class->estudante_id,
                ':ano_letivo' => $class->ano_letivo,
                ':public_id' => $class->public_id,
                ':quantidade_assinaturas' => $class->quantidade_assinaturas ?? null
            ]);

            if (!$create) {
                return null;
            }

            $created = $this->findByUuid($class->uuid);
            return $created;
        } catch (\Throwable $th) {
            dd($th->getMessage());
            LoggerHelper::logInfo("Erro na transação create: {$th->getMessage()}");
            LoggerHelper::logInfo("Trace: " . $th->getTraceAsString());
            return null;
        } finally {          
            Database::getInstance()->closeConnection();
        }
    }

    // public function update(array $data, int $id) 
    // {
    //     $class = $this->model->create($data);

    //     try {
    //         $stmt = $this->conn->prepare(
    //             "UPDATE " . self::TABLE . " 
    //             SET 
    //                 url_contrato = :url_contrato,
    //                 url_contrato_assinado = :url_contrato_assinado
    //             WHERE id = :id
    //             "
    //         );

    //         $update = $stmt->execute([
    //             ':url_contrato' => $class->url_contrato,
    //             ':url_contrato_assinado' => $class->url_contrato_assinado,
    //             ':id' => $id
    //         ]);

    //         if (!$update) {
    //             return null;
    //         }

    //         return $this->findById($id);
    //     } catch (\Throwable $th) {
    //         LoggerHelper::logInfo("Erro na transação update: {$th->getMessage()}");
    //         LoggerHelper::logInfo("Trace: " . $th->getTraceAsString());
    //         return null;
    //     } finally {          
    //         Database::getInstance()->closeConnection();
    //     }
    // }

}