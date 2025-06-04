<?php

namespace App\Interfaces\Work_Load;

interface ICargaHorariaRepository{

    public function allWorkLoad(array $params = []);

    public function create(array $data);

    public function update(array $data, int $id);

    public function delete(int $id);

    public function findByUuid(string $uuid);

    public function findById(string $id);

}