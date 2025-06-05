<?php

$router->create('GET', '/site-albuns', [$siteAlbumController, 'index'], $auth);
$router->create('GET', '/site-albuns/criar', [$siteAlbumController, 'create'], $auth);
$router->create('POST', '/site-albuns/criar', [$siteAlbumController, 'store'], $auth);
$router->create('GET', '/site-albuns/{id}/editar', [$siteAlbumController, 'edit'], $auth);
$router->create('POST', '/site-albuns/{id}/editar', [$siteAlbumController, 'update'], $auth);
$router->create('DELETE', '/site-albuns/{id}', [$siteAlbumController, 'destroy'], $auth);