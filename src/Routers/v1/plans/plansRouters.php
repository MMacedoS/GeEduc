<?php

$router->create("GET", "/planos", [$planoController, "index"], $auth);
$router->create("GET", "/planos/criar", [$planoController, "create"], $auth);
$router->create("POST", "/planos/criar", [$planoController, "store"], $auth);
$router->create("GET", "/planos/{id}/editar", [$planoController, "edit"], $auth);
$router->create( "POST", "/planos/{id}/editar", [$planoController, "update"], $auth);
$router->create("DELETE", "/planos/{id}", [$planoController, "destroy"], $auth);
