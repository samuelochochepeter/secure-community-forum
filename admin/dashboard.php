<?php

require_once '../config/config.php';
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| Admin Check
|--------------------------------------------------------------------------
*/

if (($_SESSION['role'] ?? '') !== 'admin') {

    $_SESSION['error'] = "Access denied.";

    header("Location: ../dashboard/index.php");

    exit();

}

/*
|--------------------------------------------------------------------------
| Dashboard Statistics
|--------------------------------------------------------------------------
*/

$stats = [];

$tables = [

    'users',
    'categories',
    'topics',
    'replies',
    'likes',
    'notifications'

];

foreach ($tables as $table) {

    $stmt = $pdo->query("SELECT COUNT(*) FROM {$table}");

    $stats[$table] = $stmt->fetchColumn();

}

/*
|--------------------------------------------------------------------------
| Recent Users
|--------------------------------------------------------------------------
*/

$recentUsers = $pdo->query("
SELECT
fullname,
username,
role,
created_at
FROM users
ORDER BY created_at DESC
LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| Recent Topics
|--------------------------------------------------------------------------
*/

$recentTopics = $pdo->query("
SELECT
title,
created_at
FROM topics
ORDER BY created_at DESC
LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| Recent Activities
|--------------------------------------------------------------------------
*/

$activities = $pdo->query("
SELECT
activity,
created_at
FROM activity_logs
ORDER BY created_at DESC
LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);

?>