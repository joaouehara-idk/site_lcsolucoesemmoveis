<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Contato;
use App\Models\User;

class ApiController extends Controller {
    public function contacts() {
        $model = new Contato();
        return $this->json($model->all());
    }

    public function users() {
        $model = new User();
        $users = $model->all();
        // Remove passwords from response
        $users = array_map(function($user) {
            unset($user['senha']);
            return $user;
        }, $users);
        return $this->json($users);
    }
}
