<?php

namespace App\Controllers\api\v1\TimeSession;

use App\Config\Auth;
use App\Interfaces\Profile\IUsuarioRepository;
use App\Repositories\File\ArquivoRepository;
use App\Repositories\Permission\PermissaoRepository;
use App\Request\Request;

class TimeSessionController extends Auth
{
    protected $usuarioRepository;

    public function __construct(
        IUsuarioRepository $usuarioRepository
    ) {
        $this->usuarioRepository = $usuarioRepository;
    }

    public function index()
    { 
        echo json_encode([
            'status' => 'success',
            'message' => 'API is working correctly'
        ]);
        http_response_code(200);
        return;
    }

    public function getToken(Request $request)
    {
        if (!$request->getJsonBody()) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid request method'
            ]);
            http_response_code(405);
            return;
        }

        $user = $this->usuarioRepository->getLogin(
            $request->getJsonBody()['username'],
            $request->getJsonBody()['password']
        );

        if (!$user) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid credentials'
            ]);
            http_response_code(401);
            return;
        }

        // if (!Authenticate::isValid()) {
        //     echo json_encode([
        //         'status' => 'error',
        //         'message' => 'Unauthorized access'
        //     ]);
        //     http_response_code(401);
        //     return;
        // }

        $token = $this->prepareToken($user);

        if (!$token) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to generate token'
            ]);
            http_response_code(500);
            return;
        }

        echo json_encode([
            'status' => 'success',
            'token' => $token
        ]);
    }

    public function verifyToken(Request $request)
    {
        $token = $request->getAuthorization();

        if (!$token) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Token is required'
            ]);
            http_response_code(400);
            return;
        }

        $userToken = $this->isValidToken($token);

        if (!is_null($userToken)) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Token is valid',
                'user' => $userToken
            ]);
            http_response_code(200);
            return;
        } 

         echo json_encode([
                'status' => 'error',
                'message' => 'Invalid token'
            ]);
            http_response_code(401);
    }
}   