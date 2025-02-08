<?php

namespace App\Interfaces\Student;

use App\Models\Student\EstudanteMensalidade;

interface IEstudanteMensalidadeRepository {

    public function allMonthlyfees(array $params = []);

    public function create(array $data);

    public function update(array $data, int $id);

    public function delete(int $id);

    public function remove($id) :?bool;

    public function getMonthlyFee(array $params = []): ?EstudanteMensalidade;

    public function findByUuid(string $uuid);

    public function findById(string $id);
    
}