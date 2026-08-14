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

if ($_SERVER['REQUEST_METHOD'] != 'POST') {

    header("Location: add-category.php");
    exit();

}

/*
|--------------------------------------------------------------------------
| GET FORM DATA
|--------------------------------------------------------------------------
*/

$name = trim($_POST['name']);
$description = trim($_POST['description']);

/*
|--------------------------------------------------------------------------
| VALIDATE INPUT
|--------------------------------------------------------------------------
*/

if (empty($name)) {

    $_SESSION['error'] = "Category name is required.";

    header("Location: add-category.php");

    exit();

}

if (strlen($name) > 100) {

    $_SESSION['error'] = "Category name must not exceed 100 characters.";

    header("Location: add-category.php");

    exit();

}

/*
|--------------------------------------------------------------------------
| CHECK DUPLICATE CATEGORY
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT id
    FROM categories
    WHERE LOWER(name) = LOWER(?)
    LIMIT 1
");

$stmt->execute([$name]);

if ($stmt->fetch()) {

    $_SESSION['error'] = "Category already exists.";

    header("Location: add-category.php");

    exit();

}

/*
|--------------------------------------------------------------------------
| READY TO INSERT
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
    | INSERT CATEGORY
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("
        INSERT INTO categories
        (
            name,
            description,
            created_at
        )
        VALUES
        (
            ?,
            ?,
            NOW()
        )
    ");

    $stmt->execute([
        $name,
        $description
    ]);

    /*
    |--------------------------------------------------------------------------
    | GET NEW CATEGORY ID
    |--------------------------------------------------------------------------
    */

    $categoryID = $pdo->lastInsertId();

    /*
    |--------------------------------------------------------------------------
    | RECORD ADMIN ACTIVITY
    |--------------------------------------------------------------------------
    */

    $activity = "Created new category: " . $name;

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

    $pdo->rollBack();

    $_SESSION['error'] = "Failed to create category.";

    header("Location: add-category.php");

    exit();

}

/*
|--------------------------------------------------------------------------
| SUCCESS
|--------------------------------------------------------------------------
*/

$_SESSION['success'] = "Category created successfully.";

header("Location: categories.php");
exit();

?>