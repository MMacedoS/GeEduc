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
        $this->loadToken();
    }

    private function loadToken()
    {
        if (file_exists($this->tokenFile)) {
            $data = json_decode(file_get_contents($this->tokenFile), true);

            if (isset($data['token'], $data['expires_at']) && time() < $data['expires_at']) {
                return $this->token = $data['token'];
            } 
            unlink($this->tokenFile); // Remove token expirado
            return;
        }
    }

    public function getToken()
    {
        if ($this->token) {
            return $this->token;
        }

        try {
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

            $data = json_decode($response->getBody(), true);
            $this->token = $data['access_token'];

            $data = json_decode($response->getBody(), true);
            $this->token = $data['access_token'];

            // Salva o token em arquivo com tempo de expiração de 1 hora
            file_put_contents($this->tokenFile, json_encode([
                'token' => $this->token,
                'expires_at' => time() + 3600, // Expira em 1 hora
            ]));

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

    public function atualizarBoleto($numeroBoleto, $dadosAtualizacao)
    {
        try {
            $response = $this->client->put("boletos/{$numeroBoleto}", [
                'headers' => $this->getHeaders(),
                'json' => $dadosAtualizacao
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

    public function cancelarBoleto($numeroBoleto)
    {
        try {
            $response = $this->client->delete("boletos/{$numeroBoleto}", [
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