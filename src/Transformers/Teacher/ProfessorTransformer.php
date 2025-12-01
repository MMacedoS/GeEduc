<?php

namespace App\Transformers\Teacher;

use App\Models\Teacher\Professor;
use App\Repositories\Person\PessoaFisicaRepository;

class ProfessorTransformer
{
    public function transform(Professor $professor)
    {
        return [
            'code' => $professor->id,
            'id' => $professor->uuid,
            'name' => $this->prepareNameTeacher($professor->pessoa_fisica_id),
            'email' => $this->prepareEmailTeacher($professor->pessoa_fisica_id),
            'active' => $professor->ativo,
            'created_at' => $professor->created_at,
            'updated_at' => $professor->updated_at,
        ];
    }

    public function transformCollection(array $professors)
    {
        return array_map([$this, 'transform'], $professors);
    }

    private function prepareNameTeacher($id)
    {
        if (is_null($id)) {
            return null;
        }

        $pessoaFisica = PessoaFisicaRepository::getInstance()->findById($id);
        return $pessoaFisica ? $pessoaFisica->nome : null;
    }

    private function prepareEmailTeacher($id)
    {
        if (is_null($id)) {
            return null;
        }

        $pessoaFisica = PessoaFisicaRepository::getInstance()->findById($id);
        return $pessoaFisica ? $pessoaFisica->email : null;
    }
}
