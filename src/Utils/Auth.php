<?php

namespace App\Utils;

class Auth {
    public static function authorize() {
        if ($_SERVER['HTTP_X_AUTH_KEY'] !== (include "../config.php")['auth_key']) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
    }
}
