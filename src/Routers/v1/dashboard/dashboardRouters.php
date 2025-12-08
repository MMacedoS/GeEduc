<?php

$router->create("GET", "/dashboard", [$dashboardController, "index"], $auth);
$router->create("GET", "/dashboard/failed-students-by-discipline", [$dashboardController, "getFailedStudentsByDiscipline"], $auth);
$router->create("GET", "/dashboard/coordinator-classes", [$dashboardController, "getCoordinatorClasses"], $auth);
$router->create("GET", "/dashboard/teacher-stats", [$dashboardController, "getTeacherStats"], $auth);
$router->create("GET", "/dashboard/student-stats", [$dashboardController, "getStudentStats"], $auth);

// Rotas baseadas no usuário logado (substitui rotas antigas)
$router->create("GET", "/dashboard/coordinator-stats", [$dashboardController, "getCoordinatorStatsByUser"], $auth);
$router->create("GET", "/dashboard/my-coordinator-stats", [$dashboardController, "getCoordinatorStatsByUser"], $auth);
$router->create("GET", "/dashboard/my-teacher-stats", [$dashboardController, "getTeacherStatsByUser"], $auth);
$router->create("GET", "/dashboard/my-student-stats", [$dashboardController, "getStudentStatsByUser"], $auth);
