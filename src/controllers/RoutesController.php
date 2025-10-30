<?php

namespace Devscode\Entreamigos\controllers;

class RoutesController{

    public function get(string $url, ?array $data){
        $let_url_u = ['listFacilities'];
        $let_url_a = ['registerFacility'];
        $other_url = ['resetPassword'];

        if (in_array($url, $let_url_u)) {
            require_once 'src/routes/user/getUser.php';
        }elseif (in_array($url, $let_url_a)) {
            require_once 'src/routes/admin/getAdmin.php';
        }elseif (in_array($url, $other_url)) {
            require_once 'src/routes/auth/getAuth.php';
        }else{
            response([
                'no existe esa url'
            ], 404);
        }
    }

    public function post(string $url, ?array $data){
        $let_url_u = [''];
        $let_url_a = ['registerFacility'];
        $other_url = ['register', 'login', 'verifyToken', 'forgotPassword', 'resetPassword', 'verifyEmail', 'logout'];

        if (in_array($url, $let_url_u)) {
             
        }elseif (in_array($url, $let_url_a)) {
            require_once 'src/routes/admin/postAdmin.php';
            
        }elseif (in_array($url, $other_url)) {
            require_once 'src/routes/auth/postAuth.php';
        }else{
            response([
                'no existe esa url'
            ], 200);
        }
    }

}