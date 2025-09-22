<?php

namespace App\Interfaces\Scores;

interface ILowScoresRepository
{
    public function getLowScoresByClass(array $params = []);

    public function getStudentsWithLowScores(array $params = []);

    public function getLowScoresStatistics(array $params = []);

    public function getFailedStudentsByDisciplineAndClass(array $params = []);

    public function getClassesForCoordinator();
}
