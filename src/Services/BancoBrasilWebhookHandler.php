<?php

namespace App\Services;

use App\Repositories\MonthlyFees\MensalidadeRepository;
use App\Repositories\Ticket\BoletoRepository;
use App\Utils\LoggerHelper;

class BancoBrasilWebhookHandler
{
    protected $boletoRepository;
    protected $mensalidadeRepository;

    public function __construct()
    {
        $this->boletoRepository = new BoletoRepository();
        $this->mensalidadeRepository = new MensalidadeRepository();
    }

    public function handle(array $boletos)
    {
        foreach ($boletos as $boleto) {
            if (!isset($boleto['id']) || !isset($boleto['codigoEstadoBaixaOperacional'])) {
                LoggerHelper::logError("Boleto inválido: " . json_encode($boleto));
                continue;
            }

            $nossoNumero = $boleto['id']; 
            $dataPagamento = date('Y-m-d', strtotime(str_replace('.', '-', $boleto['dataLiquidacao'])));
            $valorPago = number_format($boleto['valorPagoSacado'] / 100, 2, '.', '');

            switch ($boleto['codigoEstadoBaixaOperacional']) {
                case 1:
                case 2:
                    $situacaoMensalidade = 'pago'; // Pago
                    break;

                case 10:
                    $situacaoMensalidade = 'cancelado'; // Cancelado
                    break;

                default:
                    LoggerHelper::logError("Código de baixa desconhecido: " . json_encode($boleto));
                    continue;
            }

            $this->boletoRepository->updateTicket($nossoNumero, $dataPagamento, $valorPago, $boleto);
            $this->mensalidadeRepository->updateMonthly($nossoNumero, $situacaoMensalidade);
        }
    }
}
