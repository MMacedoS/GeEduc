<?php

$router->create('GET', "/relatorios/{id}/gerar-grade", [$gradeReportController, 'indexTeacher'], $auth);

$router->create('GET', "/relatorios/{id}/grade-notas", [$gradeReportController, 'indexStudents'], $auth);
$router->create('GET', "/relatorios/turma/{id}/boletins", [$gradeReportController, 'boletins'], $auth);

$router->create('GET', "/relatorio/turma/{turma}/estudantes", [$gradeReportController, 'allTicketsDetails'], $auth);
$router->create('GET', "/relatorio/turma/{turma}/estudante/{student}", [$gradeReportController, 'ticketsDetailsByStudent'], $auth);
