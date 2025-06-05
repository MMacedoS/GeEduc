<?php 

$router->create( "GET", "/carga-horaria", [$cargaHorariaController, "index"], $auth);
$router->create( "GET", "/carga-horaria/criar", [$cargaHorariaController, "create"], $auth);
$router->create( "POST", "/carga-horaria/criar", [$cargaHorariaController, "store"], $auth);
$router->create( "GET", "/carga-horaria/{id}/editar", [$cargaHorariaController, "edit"], $auth);
$router->create( "POST", "/carga-horaria/{id}/editar", [$cargaHorariaController, "update"], $auth);
$router->create( "DELETE", "/carga-horaria/{id}", [$cargaHorariaController, "destroy"], $auth);
