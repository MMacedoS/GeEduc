<?php

$router->create("GET", "/bancos", [$contaBancariaController, "index"], $auth);
$router->create( "GET", "/bancos/criar", [$contaBancariaController, "create"], $auth);
$router->create( "POST", "/bancos/criar", [$contaBancariaController, "store"], $auth);
$router->create( "GET", "/bancos/{id}/editar", [$contaBancariaController, "edit"], $auth);
$router->create( "POST", "/bancos/{id}/editar", [$contaBancariaController, "update"], $auth);
$router->create( "DELETE", "/bancos/{id}", [$contaBancariaController, "destroy"], $auth);
