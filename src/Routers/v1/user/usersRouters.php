<?php

$router->create("GET", "/usuarios", [$usuarioController, "index"], $auth);
$router->create("GET", "/usuario", [$usuarioController, "create"], $auth);
$router->create("POST", "/usuario/add/", [$usuarioController, "store"], $auth);
$router->create("GET", "/usuario/{id}/editar",[$usuarioController, "edit"], $auth);
$router->create("POST", "/usuario/{id}/upt", [$usuarioController, "update"], $auth);
$router->create("POST", "/usuario/{id}/deletar",[$usuarioController, "delete"], $auth);
$router->create("GET", "/usuario/{id}/permissao", [$usuarioController, "permissao"], $auth);
$router->create("POST", "/usuario/{id}/permissao", [$usuarioController, "add_permissao"], $auth);
$router->create("GET", "/usuario/{id}/active", [$usuarioController, "changeStatus"], $auth);
// $router->create("GET", "/usuario/", [$usuarioController, "index"], $auth);
