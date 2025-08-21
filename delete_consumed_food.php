<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

$consumed_food_id = isset($input['id']) ? intval($input['id']) : 0;
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

if ($consumed_food_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid consumed food ID.']);
    exit;
}

if ($user_id <= 0) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM consumed_foods WHERE id = ? AND user_id = ?");
    $stmt->execute([$consumed_food_id, $user_id]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Consumed food deleted successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Consumed food not found or not authorized.']);
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
