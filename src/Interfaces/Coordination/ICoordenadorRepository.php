<?php

namespace App\Interfaces\Coordination;

use App\Models\Coordination\Coordenador;

interface ICoordenadorRepository {

    public function allCoordinators(array $params = []);

    public function saveAll(array $data): ?Coordenador;

    public function create(array $data): ?Coordenador;

    public function update(array $data, int $id) : ?Coordenador;

    public function updateAll(array $data): ?Coordenador;

    public function delete(int $id);

    public function deleteAll(Coordenador $coordenador);

    public function findByUuid(string $uuid);

    public function findById(string $id);
}