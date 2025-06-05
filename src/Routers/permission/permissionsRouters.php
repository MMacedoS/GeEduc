<?php

$router->create("POST", "/permissao/{id}/deletar", [$permissaoController, "delete"], $auth);
$router->create("GET", "/permissao/{id}/editar",[$permissaoController, "edit"], $auth);
$router->create("POST", "/permissao/{id}/upt", [$permissaoController, "update"], $auth);
$router->create("POST", "/permissao/add/", [$permissaoController, "store"], $auth);
$router->create("GET", "/permissao/criar", [$permissaoController, "create"], $auth);
$router->create("GET", "/permissao/{request}", [$permissaoController, "index"], $auth);
$router->create("GET", "/permissao/", [$permissaoController, "index"], $auth);
