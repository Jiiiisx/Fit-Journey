<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
$today = date('Y-m-d');

$response = [
    'success' => false,
    'daily_activities' => [],
    'total_burned_calories' => 0
];

if ($user_id <= 0) {
    $response['message'] = 'User not logged in';
    echo json_encode($response);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id, activity_name, duration_minutes, calories_burned, tanggal, created_at FROM daily_activities WHERE user_id = ? AND tanggal = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id, $today]);
    $daily_activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $total_burned_calories = 0;
    foreach ($daily_activities as $activity) {
        $total_burned_calories += floatval($activity['calories_burned']);
    }

    $response['success'] = true;
    $response['daily_activities'] = $daily_activities;
    $response['total_burned_calories'] = $total_burned_calories;

} catch (PDOException $e) {
    $response['message'] = 'Database error: ' . $e->getMessage();
}

echo json_encode($response);
