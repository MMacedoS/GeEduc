<?php

namespace App\Repositories\Ticket;

use App\Config\Database;
use App\Interfaces\Ticket\IBoletoRepository;
use App\Models\Ticket\Boleto;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;

class BoletoRepository implements IBoletoRepository {
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
                    nosso_numero = :nosso_numero,
                    pix = :pix,
                    boleto = :boleto,
                    conta_bancaria_id = :conta_bancaria_id
                "
            );

            // Verifique se todos os parâmetros estão corretos e fornecidos
            $create = $stmt->execute([
                ':uuid' => $monthly->uuid,
                ':mensalidade_id' => $monthly->mensalidade_id,
                ':valor' => $monthly->valor,
                ':data_vencimento' => $monthly->data,  // Verifique o nome do campo, deve ser `data_vencimento` em vez de `data`
                ':codigo_barras' => $monthly->codigo_barras,
                ':nosso_numero' => $monthly->nosso_numero,
                ':pix' => $monthly->pix,
                ':boleto' => json_encode($monthly->boleto),
                ':conta_bancaria_id' => $monthly->conta_bancaria_id
            ]);

            if (!$create) {
                LoggerHelper::logError('Falha na inserção do boleto.', ['params' => $params]);
                throw new \Exception('Falha ao criar o boleto.');
            }

            return $this->findByUuid($monthly->uuid);

        } catch (\Throwable $th) {
            LoggerHelper::logError("Erro ao criar mensalidade: " . $th->getMessage(), ['exception' => $th]);
            throw $th; // Lança a exceção para ser tratada externamente
        } finally {          
            Database::getInstance()->closeConnection();
        }
    }

    public function ticketByMonthlyId(int $monthly_id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM " . self::TABLE . " where mensalidade_id = :id");
        $stmt->bindParam(':id', $monthly_id, \PDO::PARAM_INT);
        $stmt->execute();
    
        
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::CLASS_NAME);
        $register = $stmt->fetch();  
        if (is_null($register)) {
            return null;
        }
    
        return $register;
    }

    public function updateTicket(string $nossoNumero, string $dataPagamento, string $valorPago, array $webhook)
    {
        $query = "UPDATE boletos SET data_pagamento = :data_pagamento, valor_pago = :valor_pago , webhook = :webhook WHERE nosso_numero = :nosso_numero";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ':data_pagamento' => $dataPagamento,
            ':valor_pago' => $valorPago,
            ':nosso_numero' => $nossoNumero,
            ':webhook' => json_encode($webhook)
        ]);

        LoggerHelper::logInfo("Boleto atualizado: Nosso Número $nossoNumero, Data Pagamento $dataPagamento, Valor Pago R$ $valorPago");
    }
}