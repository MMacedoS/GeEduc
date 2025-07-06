<?php

namespace App\Interfaces\Classrooms;

interface ITurmaDisciplinaRepository {
    
    public function allClassDisciplines(array $params = []);

    public function create(array $data);

    public function update(array $data, int $id);

    public function delete(int $id);

    public function classDisciplinesByTeacherDisciplineId(int $teacherDisciplineId);

    public function classDisciplinesByTeacherId(int $teacherId);

    public function findByUuid(string $uuid);

    public function findById(string $id);

    public function classDisciplineByParams(array $params = []);
}