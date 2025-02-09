<?php

namespace App\Interfaces\Contracts;

interface IContratoRepository {
    public function create(array $data);
    public function updateSignature(string $data);
}