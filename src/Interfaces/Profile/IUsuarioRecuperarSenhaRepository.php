<?php

namespace App\Interfaces\Profile;

interface IUsuarioRecuperarSenhaRepository {

    public function create($user_email);

    public function updatePassword(array $data, int $user_id);

    public function delete(int $id);

    public function findByUuid(string $uuid);

    public function findById(string $id);
}