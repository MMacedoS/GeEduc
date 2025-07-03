<?php

namespace App\Interfaces\Recuperation;

interface IRecuperacaoRepository 
{
    public function all(array $params = []);
    public function studentByScoreLow(array $params = []);
    public function create(array $params);
    public function findById(int $id);
    public function findByUuid(string $uuid);
}