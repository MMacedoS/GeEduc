<?php

namespace App\Interfaces\Plan;

use App\Models\Plan\Plano;

interface IPlanoRepository {

    public function allPlans(array $params = []);

    public function create(array $data);

    public function update(array $data, int $id);

    public function delete(int $id);

    public function planByAmmount(string $valor): ?Plano;

    public function findByUuid(string $uuid);

    public function findById(string $id);
}