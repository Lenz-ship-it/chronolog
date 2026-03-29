<?php
require_once __DIR__ . '/cors.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/utils.php';
require_once __DIR__ . '/vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$action = $_GET['action'] ?? '';
$secretKey = getenv('JWT_SECRET'); 

try {
    switch ($action) {

        /* ========== REGISTER ========== */
        case 'register':
            $name = trim($input['name'] ?? '');
            $username = trim($input['username'] ?? '');
            $password = $input['password'] ?? '';

            if (!$username || !$password) {
                jsonResponse(['error' => 'Username and password are required'], 400);
            }

            $check = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $check->execute([$username]);
            if ($check->fetch()) {
                jsonResponse(['error' => 'Username already taken'], 400);
            }

            $hashed = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO users (name, username, password) VALUES (?, ?, ?)");
            $stmt->execute([$name, $username, $hashed]);

            jsonResponse(['success' => true, 'message' => 'Registration successful!']);
            break;

        /* ========== LOGIN ========== */
        case 'login':
            $username = trim($input['username'] ?? '');
            $password = $input['password'] ?? '';

            if (!$username || !$password) {
                jsonResponse(['error' => 'Username and password required'], 400);
            }

            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user || !password_verify($password, $user['password'])) {
                jsonResponse(['error' => 'Username / Password Salah'], 401);
            }

            $payload = [
                'id' => $user['id'],
                'username' => $user['username'],
                'exp' => time() + (60 * 60 * 24 * 3)
            ];

            $jwt = JWT::encode($payload, $secretKey, 'HS256');

            jsonResponse([
                'success' => true,
                'token' => $jwt,
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username']
                ]
            ]);
            break;
        case 'resetPassword':
            $username = trim($input['username'] ?? '');
            $newpass = $input['password'] ?? '';

            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $exists = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($exists) {
                $hashed = password_hash($newpass, PASSWORD_BCRYPT);
                $upd = $pdo->prepare("UPDATE users SET password = ? WHERE username = ?");
                $upd->execute([$hashed, $username]);
            }

            jsonResponse(['success' => true, 'message' => 'Password updated']);
            break;

        default:
            jsonResponse(['error' => 'Invalid auth action'], 400);
    }

} catch (Exception $e) {
    jsonResponse(['error' => $e->getMessage()], 500);
}
