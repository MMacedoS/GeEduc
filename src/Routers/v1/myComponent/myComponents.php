<?php

$router->create("GET", "/meus-componentes/{id}/disciplina/{turma_disciplina}/atividades", [$atividadeController, "index"], $auth);
$router->create("GET", "/meus-componentes/{id}/disciplina/{turma_disciplina}/atividade", [$atividadeController, "create"], $auth);
$router->create("POST", "/meus-componentes/{id}/disciplina/{turma_disciplina}/atividade", [$atividadeController, "store"], $auth);
$router->create("GET", "/meus-componentes/{id}/disciplina/{turma_disciplina}/atividade/{atividade_id}", [$atividadeController, "edit"], $auth);
$router->create("POST", "/meus-componentes/{id}/disciplina/{turma_disciplina}/atividade/{atividade_id}", [$atividadeController, "update"], $auth);
$router->create("DELETE", "/meus-componentes/{id}/disciplina/{turma_disciplina}/atividade/{atividade_id}", [$atividadeController, "destroy"], $auth);

$router->create('GET', "/meus-componentes/turma/{turma_id}/disciplina/{disciplina_id}/recuperacoes", [$recuperacaoController, 'index'], $auth);

$router->create('POST', "/meus-componentes/turma/{turma_id}/disciplina/{disciplina_id}/recuperacao", [$recuperacaoController, 'store'], $auth);

$router->create('POST', "/meus-componentes/turma/{turma_id}/disciplina/{disciplina_id}/exame-final", [$recuperacaoController, 'storeFinalExam'], $auth);

$router->create("GET", "/meus-componentes/", [$professorController, "indexTeacher"], $auth);
$router->create('GET', "/meus-componentes/{id}/frequencia", [$frequenciaController, 'indexTeacher'], $auth);
$router->create('POST', "/meus-componentes/{id}/frequencia", [$frequenciaController, 'store'], $auth);
$router->create('GET', "/meus-componentes/{id}/notas", [$notaController, 'indexTeacher'], $auth);
$router->create('POST', "/meus-componentes/{id}/notas", [$notaController, 'store'], $auth);
