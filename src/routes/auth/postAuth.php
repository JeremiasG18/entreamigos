<?php

use Devscode\Entreamigos\controllers\AuthController;
use Devscode\Entreamigos\repository\AuthRepository;
use Devscode\Entreamigos\validators\AuthValidator;
use PHPMailer\PHPMailer\PHPMailer;

$auth_repository = new AuthRepository;
$auth_validator = new AuthValidator;
$phpmailer = new PHPMailer(true);
$auth = new AuthController($auth_repository, $auth_validator, $phpmailer);

switch ($url) {
    case 'register':
        $auth->register();
        break;
    case 'login':
        $auth->login();
    case 'forgotPassword':
        $auth->forgotPassword();
    case 'verifyToken':
        $auth->verifyToken($data);
    case 'resetPassword':
        $auth->resetPassword($data);
    case 'verifyEmail':
        $auth->verifyEmail();
    default:
        response([
            'status' => 'error',
            'message' => 'url no encontrado'
        ], 404);
        break;
}