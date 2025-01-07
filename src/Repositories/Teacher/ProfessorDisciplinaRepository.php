<?php

namespace App\Repositories\Teacher;

use App\Config\Database;
use App\Models\Teacher\ProfessorDisciplina;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;

class ProfessorDisciplinaRepository {

    const CLASS_NAME = ProfessorDisciplina::class;
    const TABLE = 'professor_disciplinas';

    use FindTrait;
}