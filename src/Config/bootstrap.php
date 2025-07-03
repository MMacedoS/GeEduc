<?php
namespace App\Config;

use App\Config\Container;
use App\Config\AppServiceProvider;

$container = new Container();
$provider = new AppServiceProvider($container);
$provider->registerDependencies();

return $container;
