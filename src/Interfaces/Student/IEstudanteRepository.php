<?php 

namespace App\Interfaces\Student;

use App\Models\Student\Estudante;

interface IEstudanteRepository {

    public function allStudents(array $params = []);

    public function studentWithPersonByUuid(string $uuid);

    public function saveAll(array $data);

    public function create(array $data);

    public function updateAll(array $data);

    public function update(array $data, int $id);

    public function deleteAll($estudante);

    public function removeAll($id);

    public function remove($id) :?bool;

    public function delete(int $id);

    public function findByStudentId(array $criteria): ?Estudante;

    public function studentByPersonId(int $person_id);

    public function findByUuid(string $uuid);

    public function findById(string $id);
}