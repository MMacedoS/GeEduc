<?php

$router->create("GET", "/minhas-turmas/", [$estudanteController, "indexStudents"], $auth);
$router->create('GET', "/minhas-turmas/{id}/estudante/{estudante}/notas", [$notaController, 'indexStudents'], $auth);
$router->create('GET', "/minhas-turmas/{id}/frequencia", [$frequenciaController, 'indexStudents'], $auth);
