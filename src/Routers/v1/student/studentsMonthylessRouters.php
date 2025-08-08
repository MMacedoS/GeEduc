<?php

$router->create( "GET", "/estudantes/{id}/mensalidades", [$estudanteMensalidadeController, "index"], $auth);
$router->create( "GET", "/estudantes/{id}/mensalidade/", [$estudanteMensalidadeController, "create"], $auth);
$router->create( "POST", "/estudantes/{id}/mensalidade/", [$estudanteMensalidadeController, "store"], $auth);
$router->create( "GET", "/estudantes/{id}/mensalidade/{mensalidade_id}/", [$estudanteMensalidadeController, "edit"], $auth);
$router->create( "POST", "/estudantes/{id}/mensalidade/{mensalidade_id}/", [$estudanteMensalidadeController, "update"], $auth);

$router->create( "DELETE", "/estudantes/{id}/mensalidade/{mensalidade_id}/", [$estudanteMensalidadeController, "destroy"], $auth);
