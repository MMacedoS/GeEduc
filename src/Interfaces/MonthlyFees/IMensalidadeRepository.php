<?php

namespace App\Interfaces\MonthlyFees;

interface IMensalidadeRepository {
    
    public function allMonthlyfees(array $params = []);

    public function create(array $data);

    public function update(array $data, int $id);

    public function delete(int $id);

    public function remove($id) :?bool;

    public function allMonthlyfeesGraph(array $params = []);

    public function findByUuid(string $uuid);

    public function findById(string $id);
}
