<?php

use Devscode\Entreamigos\controllers\AuthController;
use Devscode\Entreamigos\repository\AuthRepository;
use Devscode\Entreamigos\validators\AuthValidator;

$auth_repository = new AuthRepository;
$auth_validator = new AuthValidator;
$auth = new AuthController($auth_repository, $auth_validator, null);

switch ($url) {
    case 'resetPassword':
        $auth->verifyToken($data);
    default:
        response([
            'status' => 'error',
            'message' => 'url no encontrado'
        ], 404);
        break;
}