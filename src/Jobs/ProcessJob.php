<?php

namespace App\Jobs;

require_once '/var/www/html/app1/vendor/autoload.php';

use App\Repositories\Bank_account\ContaBancariaRepository;
use App\Repositories\MonthlyFees\MensalidadeRepository;
use App\Repositories\Ticket\BoletoRepository;
use App\Services\BoletoBBService;
use App\Utils\BoletoTrait;
use App\Utils\LoggerHelper;

class ProcessJob
{
    use BoletoTrait;

    private $mensalidadeRepository;
    private $boletoservice;
    private $contaBancariaRepository;
    private $boletoRepository;

    public function __construct() {
        $this->mensalidadeRepository = new MensalidadeRepository();
        $this->boletoservice = new BoletoBBService();
        $this->contaBancariaRepository = new ContaBancariaRepository();
        $this->boletoRepository = new BoletoRepository();
    }

    public function handle()
    {
        $mensalidades = $this->mensalidadeRepository->monthleesByStudentForBoleto();
        $banco = $this->contaBancariaRepository->findById(1);

        if (empty($mensalidades)) {
            LoggerHelper::logInfo(Date('Y-m-d H:i:s') . " Erro ao gerar :" . count($mensalidades));
            return true;
        }

        foreach ($mensalidades as $key => $value) {
            $dados = $this->prepareTicketData($value, $banco);
            $boleto = $this->boletoservice->emitirBoleto($dados);
            $resBoleto = $this->getLinhaDigitavelAndQRCode($boleto);
            if (!is_null($boleto)) {
                $this->boletoRepository->create([
                    'monthly_id' => $value->id,
                    'bank_id' => $banco->id,
                    'data' => $value->data_vencimento,
                    'ticket' => $boleto,
                    'amount' => $value->valor,
                    'barcode' => $resBoleto['linha_digitavel'] ?? null,
                    'pix' => $resBoleto['qrcode'] ?? null
                ]);

                $this->mensalidadeRepository->updateGerouBoleto($value->id);
            }
        }
    }

    private function getLinhaDigitavelAndQRCode($json)
    {
        $data = json_decode($json, true);

        if (isset($data['data']['linhaDigitavel']) && isset($data['data']['qrCode']['url'])) {
            return [
                'linha_digitavel' => $data['data']['linhaDigitavel'],
                'qrcode' => $data['data']['qrCode']['url']
            ];
        }

        return null;
    }
}

$job = new ProcessJob();
$job->handle();
