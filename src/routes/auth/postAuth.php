<?php

use Devscode\Entreamigos\controllers\UserController;
use Devscode\Entreamigos\repository\UserRepository;
use Devscode\Entreamigos\validators\UserValidator;
use PHPMailer\PHPMailer\PHPMailer;

$user_repository = new UserRepository;
$user_validator = new UserValidator;
$phpmailer = new PHPMailer(true);
$user = new UserController($user_repository, $user_validator, $phpmailer);

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
    case 'verifyToken':
        $user->verifyToken($data);
    case 'resetPassword':
        $user->resetPassword($data);
    default:
        response([
            'status' => 'error',
            'message' => 'url no encontrado'
        ], 404);
        break;
}