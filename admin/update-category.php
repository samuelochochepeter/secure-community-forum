<?php

session_start();

require_once '../config/config.php';
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {

    header("Location: ../login.php");
    exit();

}

if ($_SESSION['role'] != 'admin') {

    $_SESSION['error'] = "Access Denied.";

    header("Location: ../dashboard/index.php");
    exit();

}

/*
|--------------------------------------------------------------------------
| VALIDATE REQUEST
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header("Location: categories.php");
    exit();

}

/*
|--------------------------------------------------------------------------
| GET FORM DATA
|--------------------------------------------------------------------------
*/

$categoryId = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$name = trim($_POST['name'] ?? '');
$description = trim($_POST['description'] ?? '');

/*
|--------------------------------------------------------------------------
| VALIDATE INPUT
|--------------------------------------------------------------------------
*/

if ($categoryId <= 0) {

    $_SESSION['error'] = "Invalid category selected.";

    header("Location: categories.php");

    exit();

}

if (empty($name)) {

    $_SESSION['error'] = "Category name is required.";

    header("Location: edit-category.php?id=" . $categoryId);

    exit();

}

if (strlen($name) > 100) {

    $_SESSION['error'] = "Category name must not exceed 100 characters.";

    header("Location: edit-category.php?id=" . $categoryId);

    exit();

}

/*
|--------------------------------------------------------------------------
| CHECK CATEGORY EXISTS
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT id
    FROM categories
    WHERE id = ?
    LIMIT 1
");

$stmt->execute([$categoryId]);

if (!$stmt->fetch(PDO::FETCH_ASSOC)) {

    $_SESSION['error'] = "Category not found.";

    header("Location: categories.php");

    exit();

}

/*
|--------------------------------------------------------------------------
| CHECK FOR DUPLICATE CATEGORY NAME
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT id
    FROM categories
    WHERE LOWER(name) = LOWER(?)
      AND id <> ?
    LIMIT 1
");

$stmt->execute([
    $name,
    $categoryId
]);

if ($stmt->fetch(PDO::FETCH_ASSOC)) {

    $_SESSION['error'] = "Another category with this name already exists.";

    header("Location: edit-category.php?id=" . $categoryId);

    exit();

}

/*
|--------------------------------------------------------------------------
| READY TO UPDATE
|--------------------------------------------------------------------------
*/

try {

    /*
    |--------------------------------------------------------------------------
    | START DATABASE TRANSACTION
    |--------------------------------------------------------------------------
    */

    $pdo->beginTransaction();

    /*
    |--------------------------------------------------------------------------
    | UPDATE CATEGORY
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("
        UPDATE categories
        SET
            name = ?,
            description = ?
        WHERE id = ?
    ");

    $stmt->execute([
        $name,
        $description,
        $categoryId
    ]);

    /*
    |--------------------------------------------------------------------------
    | RECORD ADMIN ACTIVITY
    |--------------------------------------------------------------------------
    */

    $activity = "Updated category: " . $name;

    $log = $pdo->prepare("
        INSERT INTO activity_logs
        (
            user_id,
            activity,
            ip_address,
            created_at
        )
        VALUES
        (
            ?,
            ?,
            ?,
            NOW()
        )
    ");

    $log->execute([

        $_SESSION['user_id'],

        $activity,

        $_SERVER['REMOTE_ADDR']

    ]);

    /*
    |--------------------------------------------------------------------------
    | COMMIT TRANSACTION
    |--------------------------------------------------------------------------
    */

    $pdo->commit();

} catch (PDOException $e) {

    /*
    |--------------------------------------------------------------------------
    | ROLLBACK TRANSACTION
    |--------------------------------------------------------------------------
    */

    if ($pdo->inTransaction()) {

        $pdo->rollBack();

    }

    $_SESSION['error'] = "Failed to update category.";

    header("Location: edit-category.php?id=" . $categoryId);

    exit();

}

/*
|--------------------------------------------------------------------------
| SUCCESS
|--------------------------------------------------------------------------
*/

$_SESSION['success'] = "Category updated successfully.";

header("Location: categories.php");
exit();

?>
