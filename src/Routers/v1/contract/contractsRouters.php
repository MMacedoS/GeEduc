<?php

$router->create('GET', '/contratos', [$contratoController, 'index'], $auth);
$router->create('GET', '/contratos/gerar', [$contratoController, 'generateAndSendContracts'], $auth);