<?php

$router->create('GET', '/site-eventos', [$siteEventoController, 'index'], $auth);
$router->create('GET', '/site-eventos/criar', [$siteEventoController, 'create'], $auth);
$router->create('POST', '/site-eventos/criar', [$siteEventoController, 'store'], $auth);
$router->create('GET', '/site-eventos/{id}/editar', [$siteEventoController, 'edit'], $auth);
$router->create('POST', '/site-eventos/{id}/editar', [$siteEventoController, 'update'], $auth);
$router->create('DELETE', '/site-eventos/{id}', [$siteEventoController, 'destroy'], $auth);

