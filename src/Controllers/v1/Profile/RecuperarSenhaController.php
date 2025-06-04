<?php

namespace App\Controllers\v1\Profile;

use App\Controllers\Controller;
use App\Interfaces\Profile\IUsuarioRecuperarSenhaRepository;
use App\Request\Request;

class RecuperarSenhaController extends Controller
{
    protected $usuarioRecuperaSenhaRepository;

    public function __construct(
        IUsuarioRecuperarSenhaRepository $usuarioRecuperaSenhaRepository
    )
    {   
        parent::__construct();
        $this->usuarioRecuperaSenhaRepository = $usuarioRecuperaSenhaRepository;
    }

    public function index(Request $request) 
    {
        return $this->router->view('login/recovery');
    }

    public function store(Request $request) 
    {
        $data = $request->getBodyParams();
        $created = $this->usuarioRecuperaSenhaRepository->create($data['email']);

        if(is_null($created)) {
            return $this->router->redirect('recuperar/');
        }

        echo '<script>alert("Solicitação iniciada! acesse a caixa de entrada ou spam do endereço de email:'. $data['email'] .' ")</script>';
        return $this->router->redirect();
    }

    public function edit(Request $request, string $id) 
    {
        $solicitation = $this->usuarioRecuperaSenhaRepository->findByUuid($id);
        
        if(is_null($solicitation) || $solicitation->ativo == 0) {
            return $this->router->redirect();
        }

        return $this->router->view('login/update-password', ['solicitation' => $solicitation]);
    }

    public function update(Request $request, string $id)
    {
        $solicitation = $this->usuarioRecuperaSenhaRepository->findByUuid($id);
        
        if(is_null($solicitation)) {
            return $this->router->redirect();
        }

        $data = $request->getBodyParams();

        $data['password_old'] = $solicitation->antiga;
        $data['id'] = $solicitation->id;

        $updated = $this->usuarioRecuperaSenhaRepository->updatePassword($data, $solicitation->usuario_id);

        echo '<script>alert("Senha atualizada!")</script>';

        return $this->router->redirect();
    } 
}