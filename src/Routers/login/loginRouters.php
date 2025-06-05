<?php

$router->create("GET", "/recuperar", [$recuperarSenhaController, "index"]);
$router->create("POST", "/recuperar", [$recuperarSenhaController, "store"]);
$router->create("GET", "/recuperar/{id}", [$recuperarSenhaController, "edit"]);
$router->create("POST", "/recuperar/{id}", [$recuperarSenhaController, "update"]);
$router->create("GET", "/", [$usuarioController, "login"], null);
$router->create("POST", "/login", [$usuarioController, "auth"]);
$router->create("GET", "/logout", [$usuarioController, "logout"], $auth);