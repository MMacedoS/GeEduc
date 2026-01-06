<?php

namespace App\Interfaces\Activitie;

interface IAtividadeRepository
{

    public function allActivities(array $params = []);

    public function create(array $params);

    public function update(array $data, int $id);

    public function delete(int $id);

    public function findByUuid(string $uuid);

    public function findById(string $id);

    public function duplicateForNewYear(int $turmaId, int $newYear): bool;
}
