<?php

ob_start();
require 'vendor/autoload.php';

$router = require 'src/Routers/web.php';

$router->init();

// require_once "manutencao.html";