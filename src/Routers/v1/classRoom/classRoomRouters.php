<?php

$router->create("GET", "/turmas", [$turmaController, "index"], $auth);
$router->create("GET", "/turma", [$turmaController, "create"], $auth);
$router->create("POST", "/turma", [$turmaController, "store"], $auth);
$router->create( "GET", "/turma/{id}", [$turmaController, "edit"], $auth);
$router->create( "POST", "/turma/{id}", [$turmaController, "update"], $auth);
$router->create("DELETE", "/turma/{id}", [$turmaController, "destroy"], $auth);

//classRooms
$router->create( "GET", "/turmas/{id}/disciplinas/", [$turmaDisciplinaController, "index"], $auth);
$router->create( "GET", "/turmas/{id}/disciplina/", [$turmaDisciplinaController, "create"], $auth);
$router->create( "POST", "/turmas/{id}/disciplina/", [$turmaDisciplinaController, "store"], $auth);
$router->create( "GET", "/turmas/{id}/disciplina/{turma_disciplina}", [$turmaDisciplinaController, "edit"], $auth);
$router->create( "POST", "/turmas/{id}/disciplina/{turma_disciplina}", [$turmaDisciplinaController, "update"], $auth);
$router->create( "DELETE", "/turmas/{id}/disciplina/{turma_disciplina}", [$turmaDisciplinaController, "destroy"], $auth);

//activities
$router->create( "GET", "/turmas/{id}/disciplinas/{turma_disciplina}/atividades", [$atividadeController, "index"], $auth);
$router->create( "GET", "/turmas/{id}/disciplinas/{turma_disciplina}/atividade", [$atividadeController, "create"], $auth);
$router->create( "POST", "/turmas/{id}/disciplinas/{turma_disciplina}/atividade", [$atividadeController, "store"], $auth);
$router->create( "GET", "/turmas/{id}/disciplinas/{turma_disciplina}/atividade/{atividade_id}", [$atividadeController, "edit"], $auth);
$router->create( "POST", "/turmas/{id}/disciplinas/{turma_disciplina}/atividade/{atividade_id}", [$atividadeController, "update"], $auth);
$router->create( "DELETE", "/turmas/{id}/disciplinas/{turma_disciplina}/atividade/{atividade_id}", [$atividadeController, "destroy"], $auth);

//activities
$router->create( "GET", "/turmas/{id}/disciplinas/{turma_disciplina}/aulas", [$aulaController, "index"], $auth);
$router->create( "GET", "/turmas/{id}/disciplinas/{turma_disciplina}/aula", [$aulaController, "create"], $auth);
$router->create( "POST", "/turmas/{id}/disciplinas/{turma_disciplina}/aula", [$aulaController, "store"], $auth);
$router->create( "GET", "/turmas/{id}/disciplinas/{turma_disciplina}/aula/{aula_id}", [$aulaController, "edit"], $auth);
$router->create( "POST", "/turmas/{id}/disciplinas/{turma_disciplina}/aula/{aula_id}", [$aulaController, "update"], $auth);
$router->create( "DELETE", "/turmas/{id}/disciplinas/{turma_disciplina}/aula/{aula_id}", [$aulaController, "destroy"], $auth);