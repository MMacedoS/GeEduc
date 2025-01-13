<?php
 
namespace App\Controllers\v1\Traits;

use App\Repositories\Person\PessoaFisicaRepository;

trait UserToPerson{
    public function authUser() 
    {
        $user = $_SESSION['user']->code;

        $pessoaFisicaRepository = new PessoaFisicaRepository();

        $pessoa = $pessoaFisicaRepository->personByUserId($user);

        return $pessoa;
    }
}