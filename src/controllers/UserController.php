<?php

namespace Devscode\Entreamigos\controllers;

use Devscode\Entreamigos\repository\UserRepository;
use Devscode\Entreamigos\validators\UserValidator;
use PDO;
use PHPMailer\PHPMailer\PHPMailer;

class UserController{

    private UserValidator $user_validator;
    private UserRepository $user_repository;
    private ?PHPMailer $phpmailer;

    public function __construct(UserRepository $user_repository, UserValidator $user_validator, ?PHPMailer $phpmailer)
    {
        $this->user_repository = $user_repository;
        $this->user_validator = $user_validator;
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
        $respuesta = $this->user_validator->validateUser($id_rol, $nombre, $apellido, $correo, $contrasena);

        if (!empty($respuesta['status']) && $respuesta['status'] === 'error') {
            response($respuesta, 422);
        }

        # Verifico si ya existe el correo en la base de datos
        if ($this->user_repository->getEmail($correo)) {
            response([
                'status' => 'error',
                'message' => 'El correo electronico ya esta registrado'
            ], 409);
        }

        # Hasheo la contraseña
        $hash = password_hash($contrasena, PASSWORD_BCRYPT);

        # Guardo y Verfico si la operación salio correctamente
        if ($this->user_repository->saveUser($id_rol, $nombre, $apellido, $correo, $hash)) {
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

    public function registerFacility(): void {
        # Recibiendo datos
        $data = $_POST;
        $img = $_FILES;

        # Verificando que se haya recibido datos
        if (!is_array($data) || count($data) === 0 || empty($img)) {
            response([
                'status' => 'error',
                'message' => 'No se recibió la información solicitada'
            ], 400);
        }

        if (empty($data['id_usuario']) || empty($data['nombre']) || empty($data['telefono']) 
            || empty($data['ubicacion']) || empty($data['longitud']) || empty($data['latitud']) 
            || empty($img['foto_complejo']) || empty($data['id_mp'])) {
            response([
                'status' => 'error',
                'message' => 'Los campos requeridos están vacíos'
            ], 422);
        }

        # Limpiando y almacenando datos
        $id_usuario = sanitize_data($data['id_usuario'], 'int');
        $nombre = sanitize_data($data['nombre']);
        $telefono = sanitize_data($data['telefono']);
        $ubicacion = sanitize_data($data['ubicacion']);
        $longitud = sanitize_data($data['longitud']);
        $latitud = sanitize_data($data['latitud']);
        $id_mp = sanitize_data($data['id_mp'], 'int');

        # Verificando que exista el usuario y que su rol sea de administrador
        $respuesta = $this->user_repository->userExistsAndIsAdmin($id_usuario);
        if (!empty($respuesta['status']) && $respuesta['status'] === 'error') {
            response($respuesta, 404);
        }

        # Verificando la integridad de los datos
        $respuesta = $this->user_validator->validateFacility($nombre, $telefono, $ubicacion, $longitud, $latitud, $id_mp);
        if (!empty($respuesta['status']) && $respuesta['status'] === 'error') {
            response($respuesta, 422);
        }

        # Subiendo la imagen del complejo
        $foto_url = upload_image($img['foto_complejo']);
        $foto_url = $foto_url['status'] === 'ok' ? $foto_url['url'] 
        : response($foto_url, 422);
        
        # Verficando si la operación salio correctamente
        if ($this->user_repository->saveFacility($id_usuario, $nombre, $telefono, $ubicacion, $longitud, $latitud, $foto_url, $id_mp)) {
            response([
                'status' => 'ok',
                'message' => 'El complejo se ha registrado exitosamente'
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

        $respuesta = $this->user_validator->validateLogin($correo, $contrasena);
        if (!empty($respuesta['status']) && $respuesta['status'] === 'error') {
            response($respuesta, 422);
        }

        if (!$this->user_repository->getEmail($correo)) {
            response([
                'status' => 'error',
                'message' => 'El usuario no ha sido registrado aún, por favor regístrese'
            ], 404);
        }

        if (password_verify($contrasena, $this->user_repository->getPassword($correo))) {
            response([
                'status' => 'ok',
                'message' => 'El usuario ha iniciado sesion'
            ], );
        }
        
        response([
            'status' => 'error',
            'message' => 'La contraseña es incorrecta'
        ], 404);
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

        if (!$this->user_repository->getEmail($correo)) {
            response([
                'status' => 'error',
                'message' => 'El usuario no ha sido registrado aún, por favor regístrese'
            ], 404);
        }

        $token = bin2hex(random_bytes(32));
        $expira = date('Y-m-d H:i:s', strtotime('+1 hour'));

        if(!$this->user_repository->saveToken($correo, $token, $expira)){
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

        if (!$this->user_repository->isUsedToken($dataurl['token'])) {
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

        $respuesta = $this->user_repository->resetPassword($hash, $respuesta['correo'], $dataurl['token']);
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

        $respuesta = $this->user_repository->searchToken($token);
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

    // public function index() {
    //     echo json_encode([["id"=>1, "name"=>"Juan"], ["id"=>2, "name"=>"Ana"]]);
    // }

    // public function show(string|int $id, ?string $nombre) {
    //     echo json_encode(["id"=>$id, "name"=>"Usuario ".$id]);
    // }

    // public function update($id) {
    //     $data = json_decode(file_get_contents("php://input"), true);
    //     echo json_encode(["message"=>"Usuario $id actualizado", "data"=>$data]);
    // }

    // public function destroy($id) {
    //     echo json_encode(["message"=>"Usuario $id eliminado"]);
    // }

    // public function showByName($nombre){
    //     echo json_encode(["message"=>"Usuario $nombre ha sido encontrado"]);
    // }
}