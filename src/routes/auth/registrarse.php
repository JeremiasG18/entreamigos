<?php

use Devscode\Entreamigos\controllers\UserController;
use Devscode\Entreamigos\repository\UserRepository;
use Devscode\Entreamigos\validators\UserValidator;

$user_repository = new UserRepository;
$user_validator = new UserValidator;
$user = new UserController($user_repository, $user_validator);

switch ($url) {
    case 'register':
        $user->register();
        break;
    case 'registerFacility':
        $user->registerFacility();
    case 'login':
        $user->login();
    case 'forgotPassword':
        $user->forgotPassword();
    default:
        response([
            'status' => 'error',
            'message' => 'url no encontrado'
        ], 404);
        break;
}