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
| VALIDATE USER ID
|--------------------------------------------------------------------------
*/

if (!isset($_GET['id'])) {

    $_SESSION['error'] = "Invalid user.";

    header("Location: users.php");

    exit();

}

$userID = (int)$_GET['id'];

/*
|--------------------------------------------------------------------------
| FETCH USER
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT *
    FROM users
    WHERE id = ?
    LIMIT 1
");

$stmt->execute([$userID]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {

    $_SESSION['error'] = "User not found.";

    header("Location: users.php");

    exit();

}

/*
|--------------------------------------------------------------------------
| PREVENT SELF SUSPENSION
|--------------------------------------------------------------------------
*/

if ($userID == $_SESSION['user_id']) {

    $_SESSION['error'] = "You cannot suspend your own account.";

    header("Location: users.php");

    exit();

}

/*
|--------------------------------------------------------------------------
| DETERMINE NEXT STATUS
|--------------------------------------------------------------------------
*/

$currentStatus = strtolower($user['status']);

switch ($currentStatus) {

    case 'active':
        $newStatus = 'suspended';
        $action = 'Suspended';
        $buttonClass = 'warning';
        break;

    case 'suspended':
        $newStatus = 'active';
        $action = 'Reactivated';
        $buttonClass = 'success';
        break;

    case 'inactive':
        $newStatus = 'active';
        $action = 'Activated';
        $buttonClass = 'primary';
        break;

    default:
        $newStatus = 'active';
        $action = 'Activated';
        $buttonClass = 'primary';
        break;

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Suspend User</title>

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

<div class="col-lg-7">

<div class="card shadow border-0">

<div class="card-header bg-<?= $buttonClass; ?> text-white">

<h4 class="mb-0">

<i class="fa-solid fa-user-lock me-2"></i>

<?= $action; ?> User Account

</h4>

</div>

<div class="card-body">

<div class="row">

    <div class="col-md-4 text-center">

        <?php

        $photo = !empty($user['profile_picture'])
            ? "../uploads/avatars/" . $user['profile_picture']
            : "../uploads/default-avatar.png";

        ?>

        <img
            src="<?= $photo; ?>"
            class="rounded-circle shadow border mb-3"
            width="170"
            height="170"
            style="object-fit:cover;">

        <h4><?= htmlspecialchars($user['fullname']); ?></h4>

        <p class="text-muted">

            @<?= htmlspecialchars($user['username']); ?>

        </p>

    </div>

    <div class="col-md-8">

        <table class="table table-bordered">

            <tr>

                <th width="35%">Email</th>

                <td><?= htmlspecialchars($user['email']); ?></td>

            </tr>

            <tr>

                <th>Role</th>

                <td>

                    <?php if ($user['role'] == "admin"): ?>

                        <span class="badge bg-danger">

                            Administrator

                        </span>

                    <?php else: ?>

                        <span class="badge bg-primary">

                            Member

                        </span>

                    <?php endif; ?>

                </td>

            </tr>

            <tr>

                <th>Current Status</th>

                <td>

                    <?php

                    if ($user['status'] == "active") {

                        echo '<span class="badge bg-success">Active</span>';

                    } elseif ($user['status'] == "inactive") {

                        echo '<span class="badge bg-secondary">Inactive</span>';

                    } elseif ($user['status'] == "suspended") {

                        echo '<span class="badge bg-warning text-dark">Suspended</span>';

                    } else {

                        echo '<span class="badge bg-danger">'
                            . htmlspecialchars($user['status']) .
                            '</span>';

                    }

                    ?>

                </td>

            </tr>

            <tr>

                <th>Next Action</th>

                <td>

                    <span class="badge bg-<?= $buttonClass; ?>">

                        <?= $action; ?>

                    </span>

                </td>

            </tr>

        </table>

        <div class="alert alert-warning mt-4">

            <i class="fa-solid fa-triangle-exclamation me-2"></i>

            <strong>Warning!</strong>

            You are about to

            <strong><?= strtolower($action); ?></strong>

            this user's account.

            This action can be reversed later by an administrator.

        </div>

        <form method="POST" action="">

            <input
                type="hidden"
                name="user_id"
                value="<?= $userID; ?>">

            <input
                type="hidden"
                name="new_status"
                value="<?= $newStatus; ?>">

            <div class="d-flex justify-content-between mt-4">

                <a
                    href="users.php"
                    class="btn btn-secondary">

                    <i class="fa-solid fa-arrow-left me-2"></i>

                    Cancel

                </a>

                <button
                    type="submit"
                    name="confirm"
                    class="btn btn-<?= $buttonClass; ?>">

                    <i class="fa-solid fa-user-lock me-2"></i>

                    <?= $action; ?> User

                </button>

            </div>

        </form>

    </div>

</div>

<?php

/*
|--------------------------------------------------------------------------
| PROCESS SUSPEND / ACTIVATE REQUEST
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm'])) {

    $userID = (int) $_POST['user_id'];
    $newStatus = trim($_POST['new_status']);

    // Allowed status values
    $allowedStatus = ['active', 'inactive', 'suspended'];

    if (!in_array($newStatus, $allowedStatus)) {

        $_SESSION['error'] = "Invalid account status.";

        header("Location: users.php");
        exit();

    }

    try {

        $pdo->beginTransaction();

        /*
        |--------------------------------------------------------------------------
        | UPDATE USER STATUS
        |--------------------------------------------------------------------------
        */

        $update = $pdo->prepare("
            UPDATE users
            SET
                status = ?,
                updated_at = NOW()
            WHERE id = ?
        ");

        $update->execute([
            $newStatus,
            $userID
        ]);

        /*
        |--------------------------------------------------------------------------
        | ACTIVITY LOG
        |--------------------------------------------------------------------------
        */

        $activity = $action . " user account: " . $user['username'];

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

        $pdo->commit();

        $_SESSION['success'] = "User account has been {$action} successfully.";

    } catch (PDOException $e) {

        $pdo->rollBack();

        $_SESSION['error'] = "Unable to update user account.";

    }

    header("Location: users.php");
    exit();

}

?>

                </div>

            </div>

        </div>

    </div>

</div>

<?php include 'includes/footer.php'; ?>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="../assets/js/script.js"></script>

</body>

</html>