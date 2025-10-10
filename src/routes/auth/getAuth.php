<?php

use Devscode\Entreamigos\controllers\UserController;
use Devscode\Entreamigos\repository\UserRepository;
use Devscode\Entreamigos\validators\UserValidator;

$user_repository = new UserRepository;
$user_validator = new UserValidator;
$user = new UserController($user_repository, $user_validator, null);

switch ($url) {
    case 'resetPassword':
        $user->verifyToken($data);
    default:
        response([
            'status' => 'error',
            'message' => 'url no encontrado'
        ], 404);
        break;
}