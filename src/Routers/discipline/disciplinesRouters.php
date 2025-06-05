<?php

$router->create("GET", "/disciplinas", [$disciplinaController, "index"], $auth);
$router->create( "GET", "/disciplinas/criar", [$disciplinaController, "create"], $auth);
$router->create( "POST", "/disciplinas/criar", [$disciplinaController, "store"], $auth);
$router->create( "GET", "/disciplinas/{id}/editar", [$disciplinaController, "edit"], $auth);
$router->create( "POST", "/disciplinas/{id}/editar", [$disciplinaController, "update"], $auth);
$router->create( "DELETE", "/disciplinas/{id}", [$disciplinaController, "destroy"], $auth);
