<?php

namespace App\Utils;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class LoggerHelper
{
    private static $logger;

    // Inicializa o logger, caso ainda não tenha sido instanciado
    private static function initLogger()
    {
        if (is_null(self::$logger)) {
            self::$logger = new Logger('app_logger');
            $logFile = __DIR__ . '/../../logs/app.log';

            // Criar diretório de logs se não existir
            $logDir = dirname($logFile);
            if (!is_dir($logDir)) {
                @mkdir($logDir, 0777, true);
            }

            // Verificar se o arquivo pode ser criado/escrito
            if (!file_exists($logFile)) {
                @touch($logFile);
                @chmod($logFile, 0666);
            }

            // Só adicionar handler se tiver permissão de escrita
            if (is_writable($logFile) || is_writable($logDir)) {
                self::$logger->pushHandler(new StreamHandler($logFile, Logger::DEBUG));
            }
        }
    }

    public static function logInfo($message)
    {
        self::initLogger();
        self::$logger->info($message);
    }

    public static function logError($message)
    {
        self::initLogger();
        self::$logger->error($message);
    }
}
