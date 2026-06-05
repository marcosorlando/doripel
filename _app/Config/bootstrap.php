<?php

declare(strict_types=1);

use App\Config\ConfigLoader;

ConfigLoader::boot();

// Gatilho de teste do ErrorHandler (apenas ambiente local):
// use ?__debug_error=1 na URL para forçar uma exceção controlada.
$host = (string)($_SERVER['HTTP_HOST'] ?? '');
$isLocalHost = in_array($host, ['localhost', '127.0.0.1'], true);
$debugError = (string)($_GET['__debug_error'] ?? '');
if ($isLocalHost && '1' === $debugError) {
    throw new RuntimeException('Erro de teste controlado para validar o layout do ErrorHandler.');
}
