<?php

use Devscode\Entreamigos\controllers\UserController;
use Devscode\Entreamigos\repository\UserRepository;

$user_repository = new UserRepository;
$user = new UserController($user_repository);

switch ($url) {
    case 'listFacilities':
        $user->listFacilities($data);
    default:
        response([
            'status' => 'error',
            'message' => 'url no encontrado'
        ], 404);
        break;
}

?>