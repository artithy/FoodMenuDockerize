<?php

namespace App\classes;

use App\Abstract\Authbase;
use App\classes\Token;
use PDO;

class Auth extends Authbase
{
    public $pdo;

    public function __construct()
    {
        $this->pdo = $this->connect();
    }

    public function create_table()
    {
        $sql = "CREATE TABLE IF NOT EXISTS user (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";

        return $this->pdo->exec($sql);
    }
    public function signup($user_name, $email, $password)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM user WHERE email = '$email'");
        $stmt->execute();

        if ($stmt->rowCount()) {
            return [
                'status' => false,
                'message' => 'User already exist'
            ];
        }

        $hashedPassword = $this->hashPassword($password);


        $stmt = $this->pdo->prepare("INSERT INTO user(user_name, email, password) VALUES ('$user_name', '$email', '$hashedPassword')");
        $stmt->execute();

        $user_id = $this->pdo->lastInsertId();

        $tokenValue = $this->generateToken();

        $token = new Token();
        $token->createTable();
        $token->create([
            'token' => $tokenValue,
            'user_id' => $user_id
        ]);

        return [
            'status' => true,
            'message' => 'Signup successful',
            'user_id' => $user_id,
            'token' => $tokenValue
        ];
    }

    public function login($email, $password)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM user WHERE email = '$email'");
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return [
                'status' => false,
                'message' => 'User not found'
            ];
        }
        if (!$this->verifyPassword($password, $user['password'])) {
            return [
                'status' => false,
                'message' => 'Incorrect password'
            ];
        }

        $tokenValue = $this->generateToken();
        $token = new Token();
        $token->create([
            'token' => $tokenValue,
            'user_id' => $user['id']
        ]);


        return [
            'status' => true,
            'message' => 'Login successful',
            'user_id' => $user['id'],
            'token' => $tokenValue

        ];
    }

    public function dashboard($token)
    {


        $stmt = $this->pdo->prepare("SELECT * FROM token WHERE token = '$token'");
        $stmt->execute();

        $tokenData = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$tokenData) {
            http_response_Code(400);
            return [
                'status' => false,
                'message' => 'Invalid or expired token'
            ];
        }

        $userId = $tokenData['user_id'];
        $userStmt = $this->pdo->prepare("SELECT id , user_name , email FROM user WHERE id = '$userId' ");
        $userStmt->execute();
        $user = $userStmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return [
                'status' => false,
                'message' => 'User not found'
            ];
        }

        return [
            'status' => true,
            'message' => 'Welcome to dashboard',
            'user' => $user

        ];
    }

    public function logout($token)
    {
        $tokenObj = new Token();
        $valid = $tokenObj->validate($token);

        if (!$valid) {
            return [
                'status' => false,
                'message' => 'Invalid token'
            ];
        }

        $tokenObj->deactivate($token);

        return [
            'status' => true,
            'message' => 'Logout successful'
        ];
    }
}
