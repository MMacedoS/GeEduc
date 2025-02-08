<?php

namespace App\Interfaces\Student;

interface IEstudanteTurmaRepository {

    public function allClassStudents(array $params = []);

    public function create(array $data);

    public function update(array $data, int $id);

    public function delete(int $id);

    public function remove($id) :?bool;

    public function studentClassByStudentId(int $student_id);

    public function findByUuid(string $uuid);

    public function findById(string $id);
}