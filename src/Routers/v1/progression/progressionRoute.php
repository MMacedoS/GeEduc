<?php

$router->create("GET", "/progression", [$progressionController, "index"], $auth);
