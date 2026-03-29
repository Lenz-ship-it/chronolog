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

function verifyToken($secretKey)
{
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? ($headers['authorization'] ?? null);

    if (empty($authHeader)) {
        jsonResponse(['error' => 'Missing Authorization header'], 401);
    }

    if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        jsonResponse(['error' => 'Invalid Authorization header format'], 401);
    }

    $token = $matches[1];

    try {
        $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
        if (empty($decoded->id)) {
            jsonResponse(['error' => 'Invalid token payload'], 401);
        }
        return $decoded->id;
    } catch (Exception $e) {
        jsonResponse(['error' => 'Invalid or expired token'], 401);
    }
}

try {
    $user_id = verifyToken($secretKey);

    switch ($action) {
        case 'quote':
            $stmt = $pdo->query("SELECT quote, author FROM quotes ORDER BY RAND() LIMIT 1");
            $quote = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($quote) {
                jsonResponse($quote);
            } else {
                jsonResponse(['quote' => 'Keep going, your story isn’t over yet.', 'author' => 'Unknown']);
            }
            break;

        case 'create':
            if (!empty($input['date']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $input['date'])) {
                $date = $input['date'] . " 00:00:00";
            } else {
                $date = date('Y-m-d H:i:s');
            }

            $stmt = $pdo->prepare("
                INSERT INTO journal_entries (user_id, feeling, reason, grateful, mindful, reflection, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $user_id,
                trim($input['feeling'] ?? ''),
                trim($input['reason'] ?? ''),
                trim($input['grateful'] ?? ''),
                trim($input['mindful'] ?? ''),
                trim($input['reflection'] ?? ''),
                $date
            ]);

            jsonResponse(['success' => true]);
            break;

        case 'read':
            $search = $_GET['search'] ?? '';
            $from = $_GET['from'] ?? null;
            $to = $_GET['to'] ?? null;

            $query = "
                SELECT id, feeling, reason, grateful, mindful, reflection, created_at
                FROM journal_entries
                WHERE user_id = ?
            ";
            $params = [$user_id];

            if (!empty($search)) {
                $query .= " AND (feeling LIKE ? OR reason LIKE ? OR grateful LIKE ? OR mindful LIKE ? OR reflection LIKE ?)";
                $searchTerm = "%$search%";
                array_push($params, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
            }

            if (!empty($from)) {
                $query .= " AND DATE(created_at) >= ?";
                $params[] = $from;
            }

            if (!empty($to)) {
                $query .= " AND DATE(created_at) <= ?";
                $params[] = $to;
            }

            $query .= " ORDER BY created_at DESC";

            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            jsonResponse($stmt->fetchAll(PDO::FETCH_ASSOC));
            break;


        case 'update':
            $id = $input['id'] ?? null;
            if (!empty($input['date']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $input['date'])) {
                $date = $input['date'] . " 00:00:00";
            } else {
                $date = date('Y-m-d H:i:s');
            }

            $stmt = $pdo->prepare("
                UPDATE journal_entries
                SET feeling=?, reason=?, grateful=?, mindful=?, reflection=?, created_at=?
                WHERE id=? AND user_id=?
            ");
            $stmt->execute([
                trim($input['feeling'] ?? ''),
                trim($input['reason'] ?? ''),
                trim($input['grateful'] ?? ''),
                trim($input['mindful'] ?? ''),
                trim($input['reflection'] ?? ''),
                $date,
                $id,
                $user_id
            ]);
            jsonResponse(['success' => true]);
            break;

        case 'delete':
            $id = $input['id'] ?? null;
            if (!$id) jsonResponse(['error' => 'Missing entry ID'], 400);

            $stmt = $pdo->prepare("DELETE FROM journal_entries WHERE id=? AND user_id=?");
            $stmt->execute([$id, $user_id]);
            jsonResponse(['success' => true]);
            break;

        case 'quote':
            $stmt = $pdo->query("SELECT * FROM quotes ORDER BY RAND() LIMIT 1");
            jsonResponse($stmt->fetch(PDO::FETCH_ASSOC));
            break;

        default:
            jsonResponse(['error' => 'Invalid journal action'], 400);
    }

} catch (Exception $e) {
    jsonResponse(['error' => $e->getMessage()], 500);
}
