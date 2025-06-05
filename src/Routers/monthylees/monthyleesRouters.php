<?php

$router->create("GET", "/mensalidades", [$mensalidadeController, 'index'], $auth);
$router->create("GET", "/mensalidade", [$mensalidadeController, 'create'], $auth);
$router->create("POST", "/mensalidade", [$mensalidadeController, 'store'], $auth);
$router->create("GET", "/mensalidade/{id}", [$mensalidadeController, 'edit'], $auth);
$router->create("POST", "/mensalidade/{id}", [$mensalidadeController, 'update'], $auth);
$router->create("DELETE", "/mensalidade/{id}", [$mensalidadeController, 'destroy'], $auth);

$router->create('GET', '/mensalidade/{uuid}/imprimir', [$mensalidadeController, 'print']);