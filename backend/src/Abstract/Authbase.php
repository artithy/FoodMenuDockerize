<?php

namespace App\Abstract;

use App\Database;
use App\traits\AuthUtils;

abstract class Authbase extends Database
{
    use AuthUtils;
    abstract public function signup($userName, $email, $password);
    // abstract public function login($email, $password);
    // abstract public function dashboard($token);
    // abstract public function logout($token);
}
