<?php

$router->create("GET", "/estudantes", [$estudanteController, "index"], $auth);
$router->create('GET', "/estudante/excel", [$estudanteController, 'createExcel'], $auth);
$router->create("GET", "/estudante", [$estudanteController, "create"], $auth);
$router->create("POST", "/estudante",[$estudanteController, "store"], $auth);
$router->create("POST", "/estudante/excel",[$estudanteController, "storeExcel"], $auth);
$router->create("GET","/estudante/{id}", [$estudanteController, "edit"], $auth);
$router->create("POST", "/estudante/{id}", [$estudanteController, "update"], $auth);
$router->create("DELETE", "/estudante/{id}", [$estudanteController, "destroy"], $auth);
