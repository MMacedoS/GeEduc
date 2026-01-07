<?php

$router->create("GET", "/progression", [$progressionController, "index"], $auth);
$router->create("GET", "/progression/available-classes", [$progressionController, "getAvailableClasses"], $auth);
$router->create("POST", "/progression/process", [$progressionController, "processProgression"], $auth);
