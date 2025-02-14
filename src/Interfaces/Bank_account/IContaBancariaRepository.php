<?php

namespace App\Interfaces\Bank_account;

use App\Models\Bank_account\ContaBancaria;

interface IContaBancariaRepository {

    public function allBanks(array $params = []);

    public function create(array $data);

    public function update(array $data, int $id): ?ContaBancaria;

    public function delete(int $id);

    public function findByUuid(string $uuid);

    public function findById(string $id);
}