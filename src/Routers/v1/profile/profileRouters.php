<?php

$router->create('GET', '/perfil', [$usuarioController, 'profile'], $auth);
$router->create('POST', '/upload', [$usuarioController, 'profileUploadPhoto'], $auth);

$router->create('GET', '/perfil/{id}', [$usuarioController, 'profile'], $auth);
$router->create('POST', '/upload', [$usuarioController, 'profileUploadPhoto'], $auth);
$router->create('POST', '/perfil', [$usuarioController, 'profileUpdate'], $auth);
$router->create('POST', '/perfil-senha', [$usuarioController, 'profilePasswordUpdate'], $auth);
