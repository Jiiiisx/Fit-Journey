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

$food_id = isset($input['food_id']) ? intval($input['food_id']) : 0;
$quantity = isset($input['quantity']) ? floatval($input['quantity']) : 0;
error_log("save_consumed_food.php - Received quantity: " . $quantity);
$consumption_date = date('Y-m-d');
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

if ($food_id <= 0 || $quantity <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid food data. Food ID and quantity must be positive.']);
    exit;
}

if ($user_id <= 0) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

try {
    // Check if this food has already been consumed by this user today
    $stmt = $pdo->prepare("SELECT id FROM consumed_foods WHERE user_id = ? AND food_id = ? AND consumption_date = ?");
    $stmt->execute([$user_id, $food_id, $consumption_date]);
    
    if ($stmt->rowCount() > 0) {
        // Update existing entry
        $stmt = $pdo->prepare("UPDATE consumed_foods SET quantity = quantity + ?, created_at = NOW() WHERE user_id = ? AND food_id = ? AND consumption_date = ?");
        $stmt->execute([$quantity, $user_id, $food_id, $consumption_date]);
    } else {
        // Insert new entry
        $stmt = $pdo->prepare("INSERT INTO consumed_foods (user_id, food_id, quantity, consumption_date) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $food_id, $quantity, $consumption_date]);
    }
    
    echo json_encode(['success' => true, 'message' => 'Food consumed successfully']);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
