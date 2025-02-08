<?php

namespace App\Interfaces\Scores;

interface INotaRepository {
    
    public function allScores(array $params = []);

    public function create(array $params);

    public function allScoresByStudents(array $params = []);

    public function findByUuid(string $uuid);

    public function findById(string $id);
}