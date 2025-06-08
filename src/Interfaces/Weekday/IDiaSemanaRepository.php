<?php

namespace App\Interfaces\Weekday;

interface IDiaSemanaRepository {

    public function allWeekDay(array $params = []);

    public function findByUuid(string $uuid);

    public function findById(string $id);

}