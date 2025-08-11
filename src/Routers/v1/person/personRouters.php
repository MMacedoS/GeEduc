<?php

$router->create('GET', '/pessoas', [$pessoaContatoController, 'index'], $auth);
$router->create('GET', '/pessoa', [$pessoaContatoController, 'create'], $auth);
$router->create('POST', '/pessoa', [$pessoaContatoController, 'store'], $auth);
$router->create('GET', '/pessoa/{id}/', [$pessoaContatoController, 'edit'], $auth);
$router->create('POST', '/pessoa/{id}/', [$pessoaContatoController, 'update'], $auth);
$router->create('DELETE', '/pessoa/{id}', [$pessoaContatoController, 'destroy'], $auth);

$router->create('GET', '/pessoas-lista', [$pessoaContatoController, 'indexWithoutPagination'], $auth);
$router->create('GET', '/minha-galerinha', [$pessoaContatoController, 'indexMyLittleGroup'], $auth);
$router->create('GET', '/minha-galerinha/estudante/{id}', [$estudanteTurmaController, 'indexHistory'], $auth);
$router->create('GET', "/minha-galerinha/estudante/{id}/turma/{class_student_id}/frequencia", [$frequenciaController, 'indexResponsibleStudents'], $auth);
$router->create('GET', "/minha-galerinha/estudante/{id}/turma/{class_student_id}/notas", [$notaController, 'indexResponsibleStudents'], $auth);

$router->create('POST', '/pessoa-responsavel', [$pessoaContatoController, 'createStudentLegalGuardian'], $auth);