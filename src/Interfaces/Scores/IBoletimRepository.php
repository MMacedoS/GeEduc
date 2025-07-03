<?php

namespace App\Interfaces\Scores;

interface IBoletimRepository 
{
    public function allSumScoresByParams(array $params = []);
    public function allRecuperationScoreByParams(array $params = []);
}