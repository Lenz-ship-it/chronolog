<?php
require_once __DIR__ . '/env.php';
require_once __DIR__ . '/vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

loadEnv(__DIR__ . '/.env');

/**
 * Send a consistent JSON response
 */
function jsonResponse($data, $code = 200)
{
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

/**
 * Get the JWT secret key from .env
 */
function getJwtSecret()
{
    $secret = getenv('JWT_SECRET');
    if (!$secret) {
        jsonResponse(['error' => 'Missing JWT secret key'], 500);
    }
    return $secret;
}

/**
 * Extract and verify JWT token from Authorization header
 * Returns the decoded user_id or throws error
 */
function verifyJwtUser($secretKey)
{
    $headers = getallheaders();

    if (empty($headers['Authorization'])) {
        jsonResponse(['error' => 'Authorization header missing'], 401);
    }

    $authHeader = trim(str_replace('Bearer', '', $headers['Authorization']));

    try {
        $decoded = JWT::decode($authHeader, new Key($secretKey, 'HS256'));
        return $decoded->id ?? null;
    } catch (Exception $e) {
        jsonResponse(['error' => 'Invalid or expired token: ' . $e->getMessage()], 401);
    }
}
