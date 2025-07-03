<?php


$router->create('GET', '/coordenadores', [$coordenadorController, 'index'], $auth);
$router->create('GET', '/coordenador', [$coordenadorController, 'create'], $auth);
$router->create('POST', '/coordenador', [$coordenadorController, 'store'], $auth);
$router->create('GET', '/coordenador/{id}/', [$coordenadorController, 'edit'], $auth);
$router->create('POST', '/coordenador/{id}/', [$coordenadorController, 'update'], $auth);
$router->create('DELETE', '/coordenador/{id}', [$coordenadorController, 'destroy'], $auth);

$router->create('GET', '/minha-coordenacao', [$turmaDisciplinaController, 'indexByCoordenador'], $auth);
$router->create("GET", "/minha-coordenacao/turma/{id}/disciplinas/", [$turmaDisciplinaController, "indexClassRoomDisciplineByCoordenador"], $auth);
$router->create('GET', "/minha-coordenacao/turma/{id}/frequencia", [$frequenciaController, 'indexTeacher'], $auth);
$router->create('POST', "/minha-coordenacao/turma/{id}/frequencia", [$frequenciaController, 'store'], $auth);
$router->create('GET', "/minha-coordenacao/turma/{id}/notas", [$notaController, 'indexTeacher'], $auth);
$router->create('POST', "/minha-coordenacao/turma/{id}/notas", [$notaController, 'store'], $auth);
$router->create("GET", "/minha-coordenacao/turma/{id}/disciplina/{turma_disciplina}/atividades", [$atividadeController, "index"], $auth);
$router->create("GET", "/minha-coordenacao/turma/{id}/disciplina/{turma_disciplina}/atividade", [$atividadeController, "create"], $auth);
$router->create("POST", "/minha-coordenacao/turma/{id}/disciplina/{turma_disciplina}/atividade", [$atividadeController, "store"], $auth);
$router->create("GET", "/minha-coordenacao/turma/{id}/disciplina/{turma_disciplina}/atividade/{atividade_id}", [$atividadeController, "edit"], $auth);
$router->create("POST", "/minha-coordenacao/turma/{id}/disciplina/{turma_disciplina}/atividade/{atividade_id}", [$atividadeController, "update"], $auth);
$router->create("DELETE", "/minha-coordenacao/turma/{id}/disciplina/{turma_disciplina}/atividade/{atividade_id}", [$atividadeController, "destroy"], $auth);
$router->create('GET', "/minha-coordenacao/turma/{turma_id}/disciplina/{disciplina_id}/recuperacao", [$recuperacaoController, 'index'], $auth);
