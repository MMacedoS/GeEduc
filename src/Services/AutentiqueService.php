<?php
namespace App\Services;

use App\Repositories\Student\EstudanteMensalidadeRepository;
use App\Utils\LoggerHelper;
use Exception;
use Dompdf\Dompdf;

class AutentiqueService
{
    protected $estudanteMensalidade;

    public function __construct()
    {
        $this->estudanteMensalidade = new EstudanteMensalidadeRepository();
    }

    public function sendContract($student, $filePath) :?string
    {
        $filePathConcat = $filePath;
        $curl = curl_init();
        $studentContract = getJsonToObject($student->contrato_infos);

        if (!file_exists($filePathConcat)) {
            return null;
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
                    "name" => "Contrato - {$studentContract->nome}"
                ],
                "signers" => [
                    [
                        "email" => "teste@teste.com",
                        // "email" => $studentContract->email,
                        "action" => "SIGN",
                        "name" => $studentContract->nome
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
            '0' => new \CURLFile($filePathConcat, 'application/pdf', 'contrato.pdf')
        ];
    
        try {
            curl_setopt_array($curl, [
                CURLOPT_URL => URL_AUTENTIQUE,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    "Authorization: Bearer " . TOKEN_AUTENTIQUE,
                    "Content-Type: multipart/form-data"
                ],
                CURLOPT_POSTFIELDS => $documento
            ]);
        
            $response = curl_exec($curl);
            curl_close($curl);
            if($response) {
                return getJsonToObject($response)->data->createDocument->id;
            }
            return null;
        } catch(Exception $e) {
            LoggerHelper::logError('Erro ao enviar o contrato: ' . $e->getMessage());
            return null;
        }
    }
}
