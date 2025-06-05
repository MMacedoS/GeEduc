<?php

$router->create("GET", "/minhas-turmas/", [$estudanteController, "indexStudents"], $auth);
$router->create('GET', "/minhas-turmas/{id}/notas", [$notaController, 'indexTeacher'], $auth);
$router->create('GET', "/minhas-turmas/{id}/frequencia", [$frequenciaController, 'indexStudents'], $auth);
