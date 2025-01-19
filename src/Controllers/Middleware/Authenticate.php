<?php

namespace App\Controllers\Middleware;

use App\Config\Auth;

class Authenticate {
    public static function handle() {
        $auth = new Auth();
        if (!$auth->check()) {            
            header('Location: ' . URL_PREFIX);           
        }
        return;
    }
}
