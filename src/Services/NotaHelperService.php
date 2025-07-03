<?php

namespace App\Services;

use App\Interfaces\Scores\INotaRepository;

class NotaHelperService
{
    protected INotaRepository $repository;

    public function __construct(INotaRepository $repository)
    {
        $this->repository = $repository;
    }

    public function notas(array $params = [])
    {
        return $this->repository->allScores($params);
    }
}
