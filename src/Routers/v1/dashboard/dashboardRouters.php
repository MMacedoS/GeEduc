<?php

$router->create("GET", "/dashboard", [$dashboardController, "index"], $auth);
$router->create("GET", "/dashboard/failed-students-by-discipline", [$dashboardController, "getFailedStudentsByDiscipline"], $auth);
$router->create("GET", "/dashboard/coordinator-classes", [$dashboardController, "getCoordinatorClasses"], $auth);
