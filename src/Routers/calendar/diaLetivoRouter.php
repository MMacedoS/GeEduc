<?php

$router->create("GET", "/dialetivos", [$diaLetivoController, "index"], $auth);
$router->create("POST", "/dialetivo", [$diaLetivoController, "store"], $auth);
$router->create("DELETE", "/dialetivo/{id}", [$diaLetivoController, "destroy"], $auth);
