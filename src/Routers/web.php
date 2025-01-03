<?php

use App\Config\Auth;
use App\Config\Router;
use App\Controllers\v1\ClassRooms\TurmaController;
use App\Controllers\v1\Dashboard\DashboardController;
use App\Controllers\v1\Permission\PermissaoController;
use App\Controllers\v1\Plan\PlanoController;
use App\Controllers\v1\Profile\UsuarioController;
use App\Controllers\v1\Teacher\ProfessorController;
use App\Controllers\v1\Student\EstudanteController;

$router = new Router();
$auth = new Auth();
$dashboardController = new DashboardController();
$usuarioController = new UsuarioController(); 
$professorController = new ProfessorController();
$estudanteController = new EstudanteController();
$permissaoController = new PermissaoController();
$planoController = new PlanoController();
$turmaController = new TurmaController();

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

$router->create('GET', '/estudantes', [$estudanteController, 'index'], $auth);
$router->create('GET', '/estudantes/criar', [$estudanteController, 'create'], $auth);
$router->create('POST', '/estudantes/criar', [$estudanteController, 'store'], $auth);
$router->create('GET', '/estudantes/{id}/editar', [$estudanteController, 'edit'], $auth);
$router->create('POST', '/estudantes/{id}/editar', [$estudanteController, 'update'], $auth);
$router->create('DELETE', '/estudantes/{id}', [$estudanteController, 'destroy'], $auth);

$router->create('GET', '/planos', [$planoController, 'index'], $auth);
$router->create('GET', '/planos/criar', [$planoController, 'create'], $auth);
$router->create('POST', '/planos/criar', [$planoController, 'store'], $auth);
$router->create('GET', '/planos/{id}/editar', [$planoController, 'edit'], $auth);
$router->create('POST', '/planos/{id}/editar', [$planoController, 'update'], $auth);
$router->create('DELETE', '/planos/{id}', [$planoController, 'destroy'], $auth);

$router->create('GET', '/turmas', [$turmaController, 'index'], $auth);
$router->create('GET', '/turmas/criar', [$turmaController, 'create'], $auth);
$router->create('POST', '/turmas/criar', [$turmaController, 'store'], $auth);
$router->create('GET', '/turmas/{id}/editar', [$turmaController, 'edit'], $auth);
$router->create('POST', '/turmas/{id}/editar', [$turmaController, 'update'], $auth);
$router->create('DELETE', '/turmas/{id}', [$turmaController, 'destroy'], $auth);