<?php

namespace App\Config;

use App\Repositories\Balance\CaixaRepository;
use App\Repositories\File\ArquivoRepository;
use App\Repositories\Permission\PermissaoRepository;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Auth {
    protected $sessionTimeout = 14400;
    protected $renewTime = 600; 

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(); 
        } 
    }

    public function login($username) 
    {
        if (is_null($username)) {
            return false; 
        }
        
        $arquivoRepository = new ArquivoRepository();
        $arquivo = $arquivoRepository->findById((int)$username->arquivo_id);           
        $permissaoRepository = new PermissaoRepository(); 
        $permissions = $permissaoRepository->allByUser((int)$username->code);

        $_SESSION['files'] = $arquivo ?? null;
        
        $token = $this->prepareToken($username);

        if (!$token) {
            return false; 
        }

        setcookie('token', $token, time() + $this->sessionTimeout);
        $_SESSION['user'] = $username;
        $_SESSION['login_time'] = time();
        $_SESSION['last_activity'] = time() + $this->sessionTimeout;
        $_SESSION['my_permissions'] = $permissions;
    
        session_regenerate_id(true);
        return true;
    }

    public function prepareToken($username) {
        if (is_null($username)) {
            return false; 
        }

        $payload = [
            'person' => (array)$username,
            'iat' => time(),
            'exp' => time() + $this->sessionTimeout,
        ];

        $token = JWT::encode($payload, SECRET_KEY,'HS256');   
        return $token;
    }

    public function isValidToken($token) {
        if (is_null($token)) {
            return false; 
        }

        try {
               $decoded = JWT::decode($token, new Key(SECRET_KEY, 'HS256'));
            
               if (!isset($decoded->person)) {
                   return null;
               }
            return $decoded;
        } catch (\Exception $e) {
            return null; 
        }
    }

    public function logout() {
        unset($_SESSION['user']);
        unset($_SESSION['login_time']);
        unset($_SESSION['last_activity']);
        setcookie('token', '', time() - 14400);
        session_destroy();
    }

    public function check(): bool
    {
        if (
            empty($_SESSION['user']) || 
            empty($_SESSION['login_time']) || 
            empty($_SESSION['last_activity'])
        ) {
            return false;
        }

        $now = time();

        if (($now - $_SESSION['login_time']) > $this->sessionTimeout) {
            $this->logout(); // método que limpa a sessão e cookies
            return false;
        }

        if (($now - $_SESSION['last_activity']) < $this->renewTime) {
            $_SESSION['last_activity'] = $now;
        }

        return true;
    }

    public function user() {
        return $_SESSION['user'] ?? null;
    }
}
