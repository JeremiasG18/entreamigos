<?php

namespace Devscode\Entreamigos\controllers;

class RoutesController{

    public function get(string $url, ?string $data){
        $let_url_u = [''];
        $let_url_a = [''];
        $other_url = ['registrarse'];

        if (in_array($url, $let_url_u)) {
             
        }elseif (in_array($url, $let_url_a)) {
            
        }elseif (in_array($url, $other_url)) {
        }else{
            response([
                'no existe esa url'
            ], 200);
        }
    }

    public function post(string $url, ?string $data){
        $let_url_u = [''];
        $let_url_a = [''];
        $other_url = ['register', 'registerFacility', 'login', 'forgotPassword'];

        if (in_array($url, $let_url_u)) {
             
        }elseif (in_array($url, $let_url_a)) {
            
        }elseif (in_array($url, $other_url)) {
            require_once 'src/routes/auth/registrarse.php';
        }else{
            response([
                'no existe esa url'
            ], 200);
        }
    }

}