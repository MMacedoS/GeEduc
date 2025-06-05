<?php

namespace App\Interfaces\Period;

interface IPeriodoRepository{

    public function all(array $criteria = []);

    public function create(array $data);

    public function update(array $data, int $id);

    public function findByUuid(string $uuid);

    public function findById(string $id);
}