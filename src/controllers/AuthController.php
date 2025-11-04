<?php

namespace Devscode\Entreamigos\controllers;

use Devscode\Entreamigos\repository\AuthRepository;
use Devscode\Entreamigos\validators\AuthValidator;
use Firebase\JWT\JWT;
use PHPMailer\PHPMailer\PHPMailer;

class AuthController{
    private AuthValidator $auth_validator;
    private AuthRepository $auth_repository;
    private ?PHPMailer $phpmailer;

    public function __construct(AuthRepository $auth_repository, AuthValidator $auth_validator, ?PHPMailer $phpmailer)
    {
        $this->auth_repository = $auth_repository;
        $this->auth_validator = $auth_validator;
        $this->phpmailer = $phpmailer;
    }

    public function register(): void {
        # Recibir datos
        $data = json_decode(file_get_contents("php://input"), true);

        # Verificar que se haya recibido datos
        if (!is_array($data) || count($data) === 0) {
            response([
                'status' => 'error',
                'message' => 'No se recibió la información solicitada'
            ], 400);
        }

        if (empty($data['id_rol']) || empty($data['nombre']) || empty($data['apellido']) || empty($data['correo']) || empty($data['contrasena'])) {
            response([
                'status' => 'error',
                'message' => 'Los campos requeridos están vacíos'
            ], 422);
        }

        # Limpiando y almacenando datos
        $id_rol = sanitize_data($data['id_rol'], 'int');
        $nombre = sanitize_data($data['nombre']);
        $apellido = sanitize_data($data['apellido']);
        $correo = sanitize_data($data['correo'], 'email');
        $contrasena = sanitize_data($data['contrasena']);

        # Valido datos del usuario
        $respuesta = $this->auth_validator->validateUser($id_rol, $nombre, $apellido, $correo, $contrasena);

        if (!empty($respuesta['status']) && $respuesta['status'] === 'error') {
            response($respuesta, 422);
        }

        # Verifico si ya existe el correo en la base de datos
        if ($this->auth_repository->getEmail($correo)) {
            response([
                'status' => 'error',
                'message' => 'El correo electronico ya esta registrado'
            ], 409);
        }

        # Hasheo la contraseña
        $hash = password_hash($contrasena, PASSWORD_BCRYPT);

        # Guardo y Verfico si la operación salio correctamente
        if ($this->auth_repository->saveUser($id_rol, $nombre, $apellido, $correo, $hash)) {
            response([
               'status' => 'ok',
                'message' => 'El usuario se ha registrado exitosamente'
            ], 200);
        }

        response([
            'status' => 'error',
            'message' => 'A ocurrido un error inesperado!, por favor intente mas tarde'
        ], 500);
    }

    public function login(): void {

        # Recibo la información
        $data = json_decode(file_get_contents("php://input"), true);

        # Verificar que se haya recibido datos
        if (!is_array($data) || count($data) === 0) {
            response([
                'status' => 'error',
                'message' => 'No se recibió la información solicitada'
            ], 400);
        }

        if (empty($data['correo']) || empty($data['contrasena'])) {
            response([
                'status' => 'error',
                'message' => 'No se completo los campos que son obligatorios'
            ], 422);
        }

        $correo = sanitize_data($data['correo'], 'email');
        $contrasena = sanitize_data($data['contrasena']);

        $respuesta = $this->auth_validator->validateLogin($correo, $contrasena);
        if (!empty($respuesta['status']) && $respuesta['status'] === 'error') {
            response($respuesta, 422);
        }

        if (!$this->auth_repository->getEmail($correo)) {
            response([
                'status' => 'error',
                'message' => 'El usuario no ha sido registrado aún, por favor regístrese'
            ], 404);
        }

        if (!password_verify($contrasena, $this->auth_repository->getPassword($correo))) {
            response([
                'status' => 'error',
                'message' => 'La contraseña es incorrecta'
            ], 404);
        }

        $usuario = $this->auth_repository->getDataUser($correo);

        $userAgent = $_SERVER['HTTP_USER_AGENT'];

        if (($usuario['id_roles'] === 1 && strpos($userAgent, 'Mozilla') !== false) || $usuario['id_roles'] === 2 && strpos($userAgent, 'okhttp') !== false) {
            $key = APP_KEY;
    
            $payload = [
                'id' => $usuario['id'],
                'email' => $correo,
                'rol' => $usuario['id_roles'],
                'iat' => time(),
                'exp' => time() + 86400,
            ];
    
            $jwt = JWT::encode($payload, $key, 'HS256');
            
            response([
                'status' => 'ok',
                'message' => 'El usuario ha iniciado sesion',
                'token' => $jwt,
                'usuario' => [
                    'id' => $usuario['id'],
                    'email' => $correo,
                    'rol' => $usuario['id_roles'],
                ]
            ], 200);
        }else{
            response([
                'status' => 'error',
                'message' => 'Los administradores solo pueden ingresar desde la web y los usuarios solo desde la app móvil'
            ], 401);
        } 
    }

    public function forgotPassword(): void {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!is_array($data) && count($data) === 0) {
            response([
                'status' => 'error',
                'message' => 'No se recibió la información solicitada'
            ], 400);
        }

        if(empty($data['correo'])){
            response([
                'status' => 'error',
                'message' => 'No se completo el campo que es obligatorio'
            ], 422);
        }

        $correo = sanitize_data($data['correo'], 'email');

        if (!validate_data('[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,10}', $correo) || strlen($correo) > 100) {
            response([
                'status' => 'error',
                'message' => 'El campo correo contiene caracteres inválidos'
            ], 422);
        }

        if (!$this->auth_repository->getEmail($correo)) {
            response([
                'status' => 'error',
                'message' => 'El usuario no ha sido registrado aún, por favor regístrese'
            ], 404);
        }

        $token = bin2hex(random_bytes(32));
        $expira = date('Y-m-d H:i:s', strtotime('+1 hour'));

        if(!$this->auth_repository->saveToken($correo, $token, $expira)){
            response([
                'status' => 'error',
                'message' => 'Hubo un error al crear el token, por favor intentelo de nuevo'
            ], 500);
        }

        submit_email($this->phpmailer, $correo, $token);

        response([
            'status' => 'ok',
            'message' => 'El correo se ha enviado correctamente, por favor verifique en su bandeja de entrada'
        ], 200);
    }

    public function verifyToken(?array $data): void {
        $respuesta = $this->verifyTokenFn($data);
        if (!empty($respuesta['status']) && $respuesta['status'] === 'error') {
            response([
                'status' => $respuesta['status'],
                'message' => $respuesta['message']
            ], $respuesta['code']);
        }

        response([
            'status' => 'ok',
            'message' => 'El token existe'
        ], 200);
    }
    
    public function resetPassword(?array $dataurl): void {
        $respuesta = $this->verifyTokenFn($dataurl);
        if (!empty($respuesta['status']) && $respuesta['status'] === 'error') {
            response([
                'status' => $respuesta['status'],
                'message' => $respuesta['message']
            ], $respuesta['code']);
        }

        if (!$this->auth_repository->isUsedToken($dataurl['token'])) {
            response([
                'status' => 'error',
                'message' => 'El token ya ha sido usado'
            ]);
        }

        $data = json_decode(file_get_contents("php://input"), true);

        if (!is_array($data) || count($data) === 0) {
            response([
                'status' => 'error',
                'message' => 'No se recibió la información solicitada'
            ], 400);
        }

        if (empty($data['contrasena'])) {
            response([
                'status' => 'error',
                'message' => 'No se completo los campos que son obligatorios'
            ], 422);
        }

        $contrasena = sanitize_data($data['contrasena']);

        if (!validate_data('(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,60}', $contrasena)) {
            response([
                'status' => 'error',
                'message' => 'El campo contraseña contiene caracteres inválidos'
            ], 422);
        }

        $hash = password_hash($contrasena, PASSWORD_BCRYPT);

        $respuesta = $this->auth_repository->resetPassword($hash, $respuesta['correo'], $dataurl['token']);
        if ($respuesta['status'] === 'error') {
            response($respuesta, 500);
        }

        response($respuesta, 200);
    }

    public function verifyTokenFn(?array $data): array {
        if (count($data) === 0) {
            return  [
                'status' => 'error',
                'message' => 'No se recibió la información solicitada',
                'code' => 400
            ];
        }

        if (empty($data['token'])) {
            return  [
                'status' => 'error',
                'message' => 'No se ha enviado el token, por favor intentelo de nuevo',
                'code' => 422
            ];
        }

        $token = sanitize_data($data['token']);
        if (!validate_data('[a-f0-9]{64}', $token)) {
            return  [
                'status' => 'error',
                'message' => 'El token contiene caracteres invalidos',
                'code' => 422
            ];
        }

        $respuesta = $this->auth_repository->searchToken($token);
        if(!$respuesta){
            return [
                'status' => 'error',
                'message' => 'No existe el token',
                'code' => 404
            ];
        }

        if (strtotime($respuesta['expiracion']) < time()) {
            return [
                'status' => 'error',
                'message' => 'El token a expirado',
                'code' => 400
            ];
        }

        return [
            'correo' => $respuesta['correo']
        ];
    }

    # no terminado :(
    public function verifyEmail(){
        $data = json_decode(file_get_contents("php://input"), true);

        # Verificar que se haya recibido datos
        if (!is_array($data) || count($data) === 0) {
            response([
                'status' => 'error',
                'message' => 'No se recibió la información solicitada'
            ], 400);
        }

        if (empty($data['correo'])) {
            response([
                'status' => 'error',
                'message' => 'No se completo los campos que son obligatorios'
            ], 422);
        }

        $correo = sanitize_data($data['correo'], 'email');
        if (!validate_data('[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,10}', $correo) || strlen($correo) > 100) {
            response([
                'status' => 'error',
                'message' => 'El campo correo contiene caracteres inválidos'
            ], 422);
        }

        // submit_email($this->phpmailer, $correo, $token);

        response([$data]);
    }

}