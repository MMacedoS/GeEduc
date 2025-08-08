<?php

$router->create( "GET", "/estudantes/{id}/turma", [$estudanteTurmaController, "studentLinkClass"], $auth);
$router->create( "POST", "/estudantes/{id}/turma/{id}", [$estudanteTurmaController, "store"], $auth);
$router->create( "PUT", "/estudantes-class/{id}", [$estudanteTurmaController, "updateStatus"], $auth);
