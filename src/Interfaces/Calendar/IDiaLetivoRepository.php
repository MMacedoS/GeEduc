<?php

namespace App\Interfaces\Calendar;

use App\Models\Calendar\DiaLetivo;

interface IDiaLetivoRepository
{
    public function all(array $params = []);
    public function create(array $data): ?DiaLetivo;
    public function firstDay(array $data): ?DiaLetivo;
    public function delete(int $id);
    public function findByUuid(string $uuid);
}
