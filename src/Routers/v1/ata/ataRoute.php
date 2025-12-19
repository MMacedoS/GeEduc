<?php

$router->create("GET", "/ata", [$ataController, "index"], $auth);
$router->create("GET", "/ata/turma/{id}", [$ataController, "create"], $auth);
$router->create("GET", "/ata/export/turma/{uuid}", [$ataController, "readScoresToAta"], $auth);
