<?php

namespace App\Controllers\Middleware;

use App\Config\Auth;

class Authenticate
{
    public static function handle(): void
    {
        $auth = new Auth();

        if (!$auth->check()) {
            // Redireciona e interrompe a execução imediatamente
            header('Location: ' . URL_PREFIX);
            exit;
        }
    }

    public static function isValid(): bool
    {
        $auth = new Auth();

        if (!$auth->check()) {
            return false;
        }
        return true;
    }
}
