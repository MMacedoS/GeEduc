<?php

namespace App\Interfaces\Scores;

interface IParalelaRepository {

    public function allScoresParallel(array $params = []);

    public function create(array $params);

    public function findByUuid(string $uuid);

    public function findById(string $id);
}