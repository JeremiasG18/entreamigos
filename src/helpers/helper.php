<?php

function respuesta(array $data, int $statusCode = 200){
    header('Content-Type: application/json; charset=utf-8');
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

function verificar_datos(string $filtro, mixed $cadena): bool{
    if (preg_match("/^" . $filtro . "$/", $cadena)) {
        return false;
    }
    return true;
}

function sanitizar_datos(string $text, string $type = 'string'): mixed {
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