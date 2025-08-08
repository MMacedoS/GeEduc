<?php

$router->create( "GET", "/periodos", [$periodoController, "index"], $auth);
$router->create( "GET", "/periodos/criar", [$periodoController, "create"], $auth);
$router->create( "POST", "/periodos/criar", [$periodoController, "store"], $auth);
$router->create( "GET", "/periodos/{id}/editar", [$periodoController, "edit"], $auth);
$router->create( "POST", "/periodos/{id}/editar", [$periodoController, "update"], $auth);
$router->create( "GET", "/periodos/{id}/active", [$periodoController, "active"], $auth);