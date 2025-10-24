<?php

use Devscode\Entreamigos\controllers\AdminController;
use Devscode\Entreamigos\repository\AdminRepository;
use Devscode\Entreamigos\validators\AdminValidator;

$admin_repository = new AdminRepository;
$admin_validator = new AdminValidator;
$admin = new AdminController($admin_repository, $admin_validator);

switch ($url) {
    case 'registerFacility':
        $admin->facility();
        break;
    
    default:
        response([
            'status' => 'error',
            'message' => 'url no encontrado'
        ], 404);
        break;
}