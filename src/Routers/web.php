<?php

use App\Config\Auth;
use App\Config\Router;
use App\Controllers\v1\Dashboard\DashboardController;
use App\Controllers\v1\Permission\PermissaoController;
use App\Controllers\v1\Profile\UsuarioController;
use App\Controllers\v1\Teacher\ProfessorController;

$router = new Router();
$auth = new Auth();
$dashboardController = new DashboardController();
$usuarioController = new UsuarioController(); 
$professorController = new ProfessorController();
$permissaoController = new PermissaoController();

$router->create('GET', '/', [$usuarioController, 'login'], null);
$router->create('POST', '/login', [$usuarioController, 'auth']);
$router->create('GET', '/logout', [$usuarioController, 'logout'], $auth);

$router->create('GET', '/dashboard', [$dashboardController, 'index'], $auth);

//users
$router->create('POST', '/usuario/{id}/deletar', [$usuarioController, 'delete'], $auth);
$router->create('GET', '/usuario/{id}/editar', [$usuarioController, 'edit'], $auth);
$router->create('POST', '/usuario/{id}/upt', [$usuarioController, 'update'], $auth);
$router->create('GET', '/usuario/{id}/permissao', [$usuarioController, 'permissao'], $auth);
$router->create('POST', '/usuario/{id}/permissao', [$usuarioController, 'add_permissao'], $auth);
$router->create('POST', '/usuario/add/', [$usuarioController, 'store'], $auth);
$router->create('GET', '/usuario/criar', [$usuarioController, 'create'], $auth);
$router->create('GET', '/usuario/{request}', [$usuarioController, 'index'], $auth);
$router->create('GET', '/usuario/', [$usuarioController, 'index'], $auth);

$router->create('POST', '/permissao/{id}/deletar', [$permissaoController, 'delete'], $auth);
$router->create('GET', '/permissao/{id}/editar', [$permissaoController, 'edit'], $auth);
$router->create('POST', '/permissao/{id}/upt', [$permissaoController, 'update'], $auth);
$router->create('POST', '/permissao/add/', [$permissaoController, 'store'], $auth);
$router->create('GET', '/permissao/criar', [$permissaoController, 'create'], $auth);
$router->create('GET', '/permissao/{request}', [$permissaoController, 'index'], $auth);
$router->create('GET', '/permissao/', [$permissaoController, 'index'], $auth);

$router->create('GET', '/professores', [$professorController, 'index'], $auth);
$router->create('GET', '/professores/criar', [$professorController, 'create'], $auth);
$router->create('POST', '/professores/criar', [$professorController, 'store'], $auth);
$router->create('GET', '/professores/{id}/editar', [$professorController, 'edit'], $auth);
$router->create('POST', '/professores/{id}/editar', [$professorController, 'update'], $auth);
$router->create('DELETE', '/professores/{id}', [$professorController, 'destroy'], $auth);
