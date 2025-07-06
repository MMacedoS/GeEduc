<?php

namespace App\Interfaces\Scores;

interface IBoletimRepository 
{
    public function allSumScoresByParams(array $params = []);
    public function allRecuperationScoreByParams(array $params = []);
    public function totalScoreByStudentsAndDisciplines(array $params = []);
    public function allcoreByStudentsAndActiviteAndPeriod(array $params = []);
}