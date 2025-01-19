<?php

use App\Utils\LoggerHelper;

require 'vendor/autoload.php';

use App\Services\DiariaServices;

try {
    $reservaService = new DiariaServices();

    $reservaService->generateDaily();

} catch (\Exception $e) {
    LoggerHelper::logError('Erro ao atualizar diárias: ' . $e->getMessage());
}
