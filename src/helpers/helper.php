<?php

function response(array $data, int $statusCode = 200){
    header('Content-Type: application/json; charset=utf-8');
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

function verificar_datos(string $filtro, mixed $cadena): bool {
    return (bool) preg_match("/^" . $filtro . "$/", $cadena);
}

function sanitize_data(string $text, string $type = 'string'): mixed {
    $text = trim($text); // quita espacios extras

    switch ($type) {
        case 'int':
            return (int) filter_var($text, FILTER_SANITIZE_NUMBER_INT);

        case 'float':
            return (double) filter_var($text, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

        case 'email':
            return (string) filter_var($text, FILTER_SANITIZE_EMAIL);

        case 'url':
            return (string) filter_var($text, FILTER_SANITIZE_URL);

        case 'string':
        default:
            $text = strip_tags($text);
            return (string) preg_replace('/[^\P{C}\n\r\t]+/u', '', $text);
    }
}

function upload_image(array $img){
    $directorio = "src/uploads/";
    $tipo_archivo_permitidos = ['image/png', 'image/jpeg', 'image/jpg'];
    $tamaño_maximo = 5 * 1024 * 1024;
    $rutaFinal = "";

    if ($img['error'] != 0) {
        return [
            'status' => 'error',
            'message' => 'Error al subir el archivo'
        ];
    }

    if (!in_array(mime_content_type($img['tmp_name']), $tipo_archivo_permitidos)) {
        return [
            'status' => 'error',
            'message' => 'El tipo de archivo no es el solicitado'
        ];
        
    }

    if ($img['size'] > $tamaño_maximo) {
        return [
            'status' => 'error',
            'message' => 'El tamaño del archivo es muy grande'
        ];
    }

    if (!file_exists($directorio)) {
        mkdir($directorio, 0777, true);
    }

    $rutaFinal = $directorio . uniqid('file_', true) . '_' . str_replace(' ', '_', basename($img['name']));
    
    if (!move_uploaded_file($img['tmp_name'], $rutaFinal)) {
        return [
            'status' => 'error',
            'message' => 'Hubo un error al guardar el archivo'
        ];
    }

    return [
        'status' => 'ok',
        'url' => $rutaFinal
    ];
}