<?php

namespace App\Models\Profile;

use App\Models\Traits\UuidTrait;

namespace App\Models\Profile;

use App\Models\Traits\UuidTrait;

class Usuario
{
    use UuidTrait;

    public $id;
    public $uuid;
    public $nome;
    public $email;
    public $senha;
    public $ativo;
    public $painel;
    public $created_at;
    public $updated_at;

    public function __construct() {}

    public function create(array $data, bool $forceNewPassword = false): Usuario
    {
        $user = new Usuario();
        $user->id = $data['id'] ?? null;
        $user->uuid = $data['uuid'] ?? $this->generateUUID();
        $user->nome = $data['name'];
        $user->email = $data['email'];
        $user->painel = $data['sector'];
        $user->ativo = $data['active'] ?? 1;

        $user->senha = $forceNewPassword
            ? $this->generatePassword($data)
            : $data['existing_password'];

        $user->created_at = $data['created_at'] ?? null;
        $user->updated_at = $data['updated_at'] ?? null;

        return $user;
    }

    private function generatePassword(array $data): string
    {
        $password = !empty($data['password']) ? $data['password'] : 'escola123';
        return password_hash($password, PASSWORD_BCRYPT);
    }
}
