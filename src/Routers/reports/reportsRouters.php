<?php

$router->create('GET', "/relatorios/{id}/gerar-grade", [$gradeReportController, 'indexTeacher'], $auth);

$router->create('GET', "/relatorios/{id}/grade-notas", [$gradeReportController, 'indexStudents'], $auth);
$router->create('GET', "/relatorios/{id}/gerar-grade", [$gradeReportController, 'indexTeacher'], $auth);
