<?php

require_once __DIR__ . '/vendor/autoload.php';

header('Content-Type: application/json; charset=utf-8');

$routes = require 'routes.php';

$function = $routes[$_REQUEST['method']] ?? 'apiGetPosts';

echo $function();
