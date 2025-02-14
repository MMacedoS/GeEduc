<?php

namespace App\Repositories\Ticket;

use App\Config\Database;
use App\Models\Ticket\Boleto;
use App\Repositories\Traits\FindTrait;

class BoletoRepository {
    const CLASS_NAME = Boleto::class;
    const TABLE = 'boletos';

    use FindTrait;
    protected $conn;
    protected $model;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
        $this->model = new Boleto();
    }

    public function create(array $params) 
    {   
        $monthly = $this->model->create($params);

        try {
            $stmt = $this->conn->prepare(
                "INSERT INTO " . self::TABLE . "
                SET
                    uuid = :uuid,
                    mensalidade_id = :mensalidade_id,
                    valor = :valor,
                    data = :data_vencimento,
                    codigo_barras = :codigo_barras,
                    pix = :pix,
                    boleto = :boleto,
                    conta_bancaria_id = :conta_bancaria_id
                "
            );

            $create = $stmt->execute([
                ':uuid' => $monthly->uuid,
                ':mensalidade_id' => $monthly->mensalidade_id,
                ':valor' => $monthly->valor,
                ':data' => $monthly->data,
                ':codigo_barras' => $monthly->codigo_barras,
                ':pix' => $monthly->pix,
                ':conta_bancaria_id' => $monthly->conta_bancaria_id
            ]);

            if (!$create) {
                return null;
            }

            return $this->findByUuid($monthly->uuid);
        } catch (\Throwable $th) {
            return null;
        }  finally {          
            Database::getInstance()->closeConnection();
        }
    }
}