<?php
namespace App\Services;

use App\Repositories\Student\EstudanteMensalidadeRepository;
use App\Utils\LoggerHelper;
use Exception;
use Dompdf\Dompdf;
use Dompdf\Options;

class AutentiqueService
{
    protected $estudanteMensalidade;

    public function __construct()
    {
        $this->estudanteMensalidade = new EstudanteMensalidadeRepository();
    }

    public function enviarContrato($estudante, $filePath)
    {
        $curl = curl_init();
        $estudanteJson = json_decode($estudante->estudantes);
    
        if (!file_exists($filePath)) {
            return ['error' => "Arquivo não encontrado: $filePath"];
        }
    
        $operations = [
            "query" => "
                mutation CreateDocument(\$file: Upload!, \$document: DocumentInput!, \$signers: [SignerInput!]!) {
                    createDocument(file: \$file, document: \$document, signers: \$signers) {
                        id
                    }
                }
            ",
            "variables" => [
                "file" => null,
                "document" => [
                    "name" => "Contrato - {$estudanteJson->nome}"
                ],
                "signers" => [
                    [
                        "email" => "teste@teste.com",
                        "action" => "SIGN",
                        "name" => $estudanteJson->nome
                    ]
                ]
            ]
        ];
    
        $map = [
            "0" => ["variables.file"]
        ];
    
        $documento = [
            'operations' => json_encode($operations),
            'map' => json_encode($map),
            '0' => new \CURLFile($filePath, 'application/pdf', 'contrato.pdf')
        ];
    
        curl_setopt_array($curl, [
            CURLOPT_URL => URL_AUTENTIQUE,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer {" . TOKEN_AUTENTIQUE . "}",
                "Content-Type: multipart/form-data"
            ],
            CURLOPT_POSTFIELDS => $documento
        ]);
    
        $response = curl_exec($curl);
        // dd($response);
        $error = curl_error($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
    
        if ($error) {
            return ['error' => $error];
        }
    
        if ($httpCode == 404) {
            return ['error' => 'Endpoint não encontrado (404). Verifique a URL da API.'];
        }
    
        return json_decode($response, true);
    }
    

    public function listarEstudantesMensalidades() {
        $estudantes = $this->estudanteMensalidade->allMonthlyfees();
        // dd($estudantes);
        $this->gerarEnviarContratos($estudantes);
        // dd($estudantes);
    }
    public function gerarEnviarContratos($estudantes) {
        foreach($estudantes as $estudante) {
            $this->gerarContratoPDF($estudante);
            // $this->enviarContrato($estudante, "contrato.pdf");
        }
    }

    public function gerarContratoPDF($estudante) {
        $htmlFilePath = __DIR__ . "/../Resources/Views/contracts/contrato.php";
    
        if (!file_exists($htmlFilePath)) {
            die("Erro: O arquivo do contrato não foi encontrado em: $htmlFilePath");
        }

        ob_start();
        extract(json_decode($estudante->contrato_infos, true));
        include $htmlFilePath;
        $html = ob_get_clean();
    
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
    
        $pdfOutput = $dompdf->output();
        file_put_contents(__DIR__ . "/../Resources/Views/contracts/contrato.pdf", $pdfOutput);
    }    
}
