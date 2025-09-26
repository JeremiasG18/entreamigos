<?php

use Devscode\Entreamigos\controllers\UserController;

$user = new UserController;

switch ($url) {
    case 'registrarse':
        $user->guardar();
        break;
    case 'registrarComplejo':
        $user->guardarComplejo();
    default:
        respuesta([
            'status' => 'error',
            'message' => 'url no encontrado'
        ], 404);
        break;
}