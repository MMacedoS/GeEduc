<?php
namespace App\Services;

use App\Utils\LoggerHelper;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class BoletoBBService
{
    private $client;
    private $baseUri;
    private $token;
    private $tokenFile = __DIR__ . '/../Storage/json/bb.json';

    public function __construct()
    {
        $this->baseUri = API_BB_URL;
        $this->client = new Client(
            [
                'base_uri' => $this->baseUri,
                'verify' => false,
            ]
        );
    }
    
    public function getToken()
    {    
        try {
            // Faz a requisição para obter o token
            $response = $this->client->post(TOKEN_URL_BB, [
                'headers' => [
                    'Authorization' => BASIC_TOKEN,
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'form_params' => [
                    'grant_type' => 'client_credentials',
                    'scope' => 'cobrancas.boletos-info cobrancas.boletos-requisicao',
                ],
            ]);
    
            // Decodifica a resposta e armazena o token
            $data = json_decode($response->getBody(), true);
            $this->token = $data['access_token'];
    
            return $this->token;
        } catch (GuzzleException $e) {
            LoggerHelper::logInfo("Erro ao obter token: " . $e->getMessage());
            return null;
        }
    }    

    private function getHeaders()
    {
        return [
            'Authorization' => 'Bearer ' . $this->getToken(),
            'Content-Type' => 'application/json',
            'gw-dev-app-key' => GW_APP_KEY
        ];
    }

    private function handleError(GuzzleException $e)
    {
        $errorMessage = $e->getMessage();
        LoggerHelper::logInfo("Erro na API de Boletos: " . $errorMessage);
        return [
            'success' => false,
            'message' => $errorMessage,
            'code' => $e->getCode()
        ];
    }

    public function emitirBoleto($dadosBoleto)
    {
        LoggerHelper::logInfo(json_encode($dadosBoleto));
        try {
            $response = $this->client->post('boletos', [
                'headers' => $this->getHeaders(),
                'json' => $dadosBoleto
            ]);
            return [
                'success' => true,
                'data' => json_decode($response->getBody(), true)
            ];
        } catch (GuzzleException $e) {
            return $this->handleError($e);
        }
    }

    public function alterarBoleto($numeroBoleto, $numeroConvenio, array $dadosAlteracao)
    {
        try {
            $response = $this->client->patch("boletos/{$numeroBoleto}", [
                'headers' => $this->getHeaders(),
                'json' => array_merge([
                    'numeroConvenio' => $numeroConvenio,
                ], $dadosAlteracao)
            ]);

            return [
                'success' => true,
                'data' => json_decode($response->getBody(), true)
            ];
        } catch (GuzzleException $e) {
            return $this->handleError($e);
        }
    }

    public function listarBoletos($filtros = [])
    {
        try {
            $response = $this->client->get('boletos', [
                'headers' => $this->getHeaders(),
                'query' => $filtros
            ]);
            return [
                'success' => true,
                'data' => json_decode($response->getBody(), true)
            ];
        } catch (GuzzleException $e) {
            return $this->handleError($e);
        }
    }

    public function cancelarBoleto($numeroBoleto, $numeroConvenio)
    {
        try {
            $response = $this->client->post("boletos/{$numeroBoleto}/baixar", [
                'headers' => $this->getHeaders(),
                'json' => [
                    'numeroConvenio' => $numeroConvenio,
                    'codigoEstadoBaixaOperacional' => 10 
                ]
            ]);

            return [
                'success' => true,
                'data' => json_decode($response->getBody(), true)
            ];
        } catch (GuzzleException $e) {
            return $this->handleError($e);
        }
    }

    public function buscarBoleto($numeroBoleto)
    {
        try {
            $response = $this->client->get("boletos/{$numeroBoleto}", [
                'headers' => $this->getHeaders()
            ]);
            return [
                'success' => true,
                'data' => json_decode($response->getBody(), true)
            ];
        } catch (GuzzleException $e) {
            return $this->handleError($e);
        }
    }
}