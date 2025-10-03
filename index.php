<?php

use Devscode\Entreamigos\controllers\RoutesController;

ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__.'/php_error_log');

require_once 'vendor/autoload.php';
require_once 'src/helpers/helper.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

$url = explode('/', $_SERVER['REQUEST_URI']);
$url = array_filter($url);

if (count($url) === 0) {
    response([
        'status' => 'error',
        'message' => 'Method not allowed'
    ], 405);
    exit;
}

$router = new RoutesController;
$uri = !empty($url[1]) ? $url[1] : '';
$data = !empty($url[2]) ? $url[2] : '';

switch ($method) {
    case 'GET':
        $router->get($uri, $data);
        break;
    case 'POST':
        $router->post($uri, $data);
        break;
    case 'PUT':

    break;
    case 'DELETE':

        break;
    default:
        break;
}