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
| VALIDATE CATEGORY ID
|--------------------------------------------------------------------------
*/

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {

    $_SESSION['error'] = "Invalid category selected.";

    header("Location: categories.php");

    exit();

}

$categoryId = (int) $_GET['id'];

/*
|--------------------------------------------------------------------------
| FETCH CATEGORY DETAILS
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT *
    FROM categories
    WHERE id = ?
    LIMIT 1
");

$stmt->execute([$categoryId]);

$category = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$category) {

    $_SESSION['error'] = "Category not found.";

    header("Location: categories.php");

    exit();

}

/*
|--------------------------------------------------------------------------
| COUNT TOPICS
|--------------------------------------------------------------------------
*/

$countStmt = $pdo->prepare("
    SELECT COUNT(*) AS total_topics
    FROM topics
    WHERE category_id = ?
");

$countStmt->execute([$categoryId]);

$totalTopics = $countStmt->fetch(PDO::FETCH_ASSOC)['total_topics'];

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Delete Category</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

<div class="d-flex">

<?php include 'includes/sidebar.php'; ?>

<div class="flex-grow-1">

<?php include 'includes/topbar.php'; ?>

<div class="container-fluid p-4">

<div class="row justify-content-center">

<div class="col-lg-8">

<div class="card shadow border-0">

<div class="card-header bg-danger text-white">

<h4 class="mb-0">

<i class="fa-solid fa-triangle-exclamation me-2"></i>

Delete Category

</h4>

</div>

<div class="card-body">

<form method="POST" action="delete-category.php">

<input
    type="hidden"
    name="id"
    value="<?= $category['id']; ?>">

<div class="alert alert-warning">

    <i class="fa-solid fa-triangle-exclamation me-2"></i>

    You are about to permanently delete this category. This action cannot be undone.

</div>

<div class="card border mb-4">

    <div class="card-header bg-light">

        <h6 class="mb-0">

            <i class="fa-solid fa-folder-open me-2 text-primary"></i>

            Category Details

        </h6>

    </div>

    <div class="card-body">

        <table class="table table-bordered align-middle mb-0">

            <tr>

                <th width="30%">Category ID</th>

                <td>#<?= $category['id']; ?></td>

            </tr>

            <tr>

                <th>Category Name</th>

                <td><?= htmlspecialchars($category['name']); ?></td>

            </tr>

            <tr>

                <th>Description</th>

                <td>

                    <?= !empty($category['description'])
                        ? nl2br(htmlspecialchars($category['description']))
                        : '<span class="text-muted">No description available.</span>'; ?>

                </td>

            </tr>

            <tr>

                <th>Topics Assigned</th>

                <td>

                    <span class="badge bg-primary">

                        <?= $totalTopics; ?> Topic<?= $totalTopics != 1 ? 's' : ''; ?>

                    </span>

                </td>

            </tr>

            <tr>

                <th>Created At</th>

                <td>

                    <?= date('F d, Y h:i A', strtotime($category['created_at'])); ?>

                </td>

            </tr>

        </table>

    </div>

</div>

<?php if ($totalTopics > 0): ?>

<div class="alert alert-danger">

    <h5>

        <i class="fa-solid fa-ban me-2"></i>

        Category Cannot Be Deleted

    </h5>

    <p class="mb-2">

        This category currently contains

        <strong><?= $totalTopics; ?></strong>

        topic<?= $totalTopics != 1 ? 's' : ''; ?>.

    </p>

    <p class="mb-0">

        Move or delete all topics assigned to this category before attempting to delete it.

    </p>

</div>

<div class="d-flex justify-content-end">

    <a href="categories.php" class="btn btn-secondary">

        <i class="fa-solid fa-arrow-left me-2"></i>

        Back to Categories

    </a>

</div>

<?php else: ?>

<div class="alert alert-danger">

    <strong>

        <i class="fa-solid fa-trash me-2"></i>

        Confirmation Required

    </strong>

    <p class="mb-0 mt-2">

        This category has no topics and is safe to remove permanently.

    </p>

</div>

<div class="d-flex justify-content-between">

    <a href="categories.php" class="btn btn-secondary">

        <i class="fa-solid fa-arrow-left me-2"></i>

        Cancel

    </a>

    <button
        type="submit"
        name="delete_category"
        class="btn btn-danger">

        <i class="fa-solid fa-trash me-2"></i>

        Delete Category

    </button>

</div>

<?php endif; ?>

<?php

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['delete_category'])
) {

    $categoryId = (int) $_POST['id'];

    try {

        /*
        |--------------------------------------------------------------------------
        | START DATABASE TRANSACTION
        |--------------------------------------------------------------------------
        */

        $pdo->beginTransaction();

        /*
        |--------------------------------------------------------------------------
        | VERIFY CATEGORY STILL EXISTS
        |--------------------------------------------------------------------------
        */

        $stmt = $pdo->prepare("
            SELECT name
            FROM categories
            WHERE id = ?
            LIMIT 1
        ");

        $stmt->execute([$categoryId]);

        $categoryData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$categoryData) {

            throw new Exception("Category not found.");

        }

        /*
        |--------------------------------------------------------------------------
        | ENSURE CATEGORY HAS NO TOPICS
        |--------------------------------------------------------------------------
        */

        $stmt = $pdo->prepare("
            SELECT COUNT(*) AS total
            FROM topics
            WHERE category_id = ?
        ");

        $stmt->execute([$categoryId]);

        $topicCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        if ($topicCount > 0) {

            throw new Exception(
                "This category still contains topics and cannot be deleted."
            );

        }

        /*
        |--------------------------------------------------------------------------
        | DELETE CATEGORY
        |--------------------------------------------------------------------------
        */

        $stmt = $pdo->prepare("
            DELETE FROM categories
            WHERE id = ?
        ");

        $stmt->execute([$categoryId]);

        /*
        |--------------------------------------------------------------------------
        | RECORD ADMIN ACTIVITY
        |--------------------------------------------------------------------------
        */

        $activity = "Deleted category: " . $categoryData['name'];

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

        $_SESSION['success'] = "Category deleted successfully.";

        header("Location: categories.php");

        exit();

    } catch (Exception $e) {

        if ($pdo->inTransaction()) {

            $pdo->rollBack();

        }

        $_SESSION['error'] = $e->getMessage();

        header("Location: delete-category.php?id=" . $categoryId);

        exit();

    }

}

?>

                </form>

            </div>

        </div>

    </div>

</div>

<?php include 'includes/footer.php'; ?>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="../assets/js/script.js"></script>

<script>

document.addEventListener("DOMContentLoaded", function () {

    const alerts = document.querySelectorAll(".alert");

    alerts.forEach(function (alert) {

        setTimeout(function () {

            alert.classList.remove("show");

            alert.classList.add("fade");

            setTimeout(function () {

                alert.remove();

            }, 500);

        }, 5000);

    });

});

</script>

</body>

</html>