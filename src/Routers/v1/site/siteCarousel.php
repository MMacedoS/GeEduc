<?php

//SiteCarrossel
$router->create('GET', '/site-carrossel', [$siteCarrosselController, 'index'], $auth);
$router->create('GET', '/site-carrossel/criar', [$siteCarrosselController, 'create'], $auth);
$router->create('POST', '/site-carrossel/criar', [$siteCarrosselController, 'store'], $auth);
$router->create('GET', '/site-carrossel/{id}/editar', [$siteCarrosselController, 'edit'], $auth);
$router->create('POST', '/site-carrossel/{id}/editar', [$siteCarrosselController, 'update'], $auth);
$router->create('DELETE', '/site-carrossel/{id}', [$siteCarrosselController, 'destroy'], $auth);
