<?php

$router->create("GET", "/api/v1/time-session", [
    $timesessionController, "index"
]);

$router->create("POST", "/api/v1/time-session/token", [
    $timesessionController, "getToken"
]);

$router->create("POST", "/api/v1/time-session/validate-token", [
    $timesessionController, "verifyToken"
]);