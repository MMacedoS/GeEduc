<?php

namespace App\Jobs;

require_once '/var/www/html/app1/vendor/autoload.php';

use App\Repositories\Student\EstudanteRepository;
use App\Utils\LoggerHelper;

class ProcessJob
{
    private $estudanteRepository;

    public function __construct() {
        $this->estudanteRepository = new EstudanteRepository();
    }

    public function handle()
    {
        $students = $this->estudanteRepository->allStudents(['active' => 1]);
        LoggerHelper::logInfo(Date('Y-m-d H:i:s') . " Estudantes:" . count($students));
    }
}

$job = new ProcessJob();
$job->handle();
