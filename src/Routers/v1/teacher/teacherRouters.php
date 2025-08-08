<?php

$router->create("GET", "/professores", [$professorController, "index"], $auth);
$router->create("GET", "/professores/criar", [$professorController, "create"], $auth);
$router->create("POST","/professores/criar", [$professorController, "store"], $auth);
$router->create("GET", "/professores/{id}/editar", [$professorController, "edit"], $auth);
$router->create("POST", "/professores/{id}/editar", [$professorController, "update"], $auth);
$router->create("DELETE", "/professores/{id}", [$professorController, "destroy"], $auth);

$router->create( "GET", "/professores/{id}/disciplina", [$professorDisciplinaController, "teacherLinkDiscipline"], $auth);
$router->create( "POST", "/professores/{id}/disciplina/{id}", [$professorDisciplinaController, "store"], $auth);
$router->create( "PUT", "/professores-disciplina/{id}", [$professorDisciplinaController, "updateStatus"], $auth);
