<?php

namespace App\Jobs;

require_once '/var/www/html/cesp-geeduc/vendor/autoload.php';

use App\Repositories\Bank_account\ContaBancariaRepository;
use App\Repositories\MonthlyFees\MensalidadeRepository;
use App\Repositories\Ticket\BoletoRepository;
use App\Services\BoletoBBService;
use App\Utils\BoletoTrait;
use App\Utils\LoggerHelper;

class ProcessJob {
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

        LoggerHelper::logInfo("Carregando " . Date('Y-m-d H:i:s'));
        
        if (empty($mensalidades)) {
            LoggerHelper::logInfo(Date('Y-m-d H:i:s') . " Erro ao gerar boletos: Nenhuma mensalidade encontrada.");
            return true;
        }
        
        foreach ($mensalidades as $key => $value) {
            $value = (object)$value;
            // Preparação dos dados do boleto
            $dados = $this->prepareTicketData($value, $banco);

            if (is_null($dados)) {
                continue;
            }
            $boleto = $this->boletoservice->emitirBoleto($dados);
        
            if (is_null($boleto)) {
                LoggerHelper::logInfo(Date('Y-m-d H:i:s') . " Erro ao emitir boleto para a mensalidade ID " . $value->id);
                continue; // Pula para a próxima mensalidade
            }
        
            // Extrai linha digitável e QR code
            $resBoleto = $this->getLinhaDigitavelAndQRCode($boleto);
        
            // Verifica se o boleto tem os dados necessários
            if (!isset($resBoleto['linha_digitavel']) || !isset($resBoleto['qrcode'])) {
                LoggerHelper::logInfo(Date('Y-m-d H:i:s') . " Boleto sem linha digitável ou QR Code para a mensalidade ID " . $value->id);
                LoggerHelper::logInfo(Date('Y-m-d H:i:s') . json_encode($boleto));
                continue; // Pula para a próxima mensalidade
            }
        
            // Registra o boleto no banco
            $this->boletoRepository->create([
                'monthly_id' => (int)$value->id,
                'bank_id' => $banco->id,
                'data' => $value->data_vencimento,
                'ticket' => $boleto,
                'amount' => $value->valor,
                'barcode' => $resBoleto['linha_digitavel'],
                'pix' => $resBoleto['qrcode'],
                'ournumber' => (string)$dados['numeroTituloCliente']
            ]);
        
            // Atualiza a mensalidade para indicar que o boleto foi gerado
            $this->mensalidadeRepository->updateGerouBoleto((int)$value->id, $dados['numeroTituloCliente']);
        
            LoggerHelper::logInfo(Date('Y-m-d H:i:s') . " Boleto gerado e registrado para a mensalidade ID " . $value->id);
        }
        
    }

    private function getLinhaDigitavelAndQRCode($data)
    {
        if (isset($data['data']['linhaDigitavel']) && isset($data['data']['qrCode']['url'])) {
            return [
                'linha_digitavel' => $data['data']['linhaDigitavel'],
                'qrcode' => $data['data']['qrCode']['emv']
            ];
        }

        return null;
    }
}

$job = new ProcessJob();
$job->handle();
