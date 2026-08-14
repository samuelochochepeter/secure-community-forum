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

    $_SESSION['error'] = "Invalid User.";

    header("Location: users.php");
    exit();

}

$userID = (int) $_GET['id'];

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
| PREVENT SELF DELETION
|--------------------------------------------------------------------------
*/

if ($_SESSION['user_id'] == $userID) {

    $_SESSION['error'] = "You cannot delete your own account.";

    header("Location: users.php");
    exit();

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Delete User</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<link
rel="stylesheet"
href="../assets/css/style.css">

</head>

<body>

<div class="d-flex">

<?php include 'includes/sidebar.php'; ?>

<div class="flex-grow-1">

<?php include 'includes/topbar.php'; ?>

<div class="container-fluid p-4">

<div class="row justify-content-center">

<div class="col-lg-8">

<div class="card border-0 shadow">

<div class="card-header bg-danger text-white">

<h4 class="mb-0">

<i class="fa-solid fa-trash-can me-2"></i>

Delete User Account

</h4>

</div>

<div class="card-body">

<div class="text-center mb-4">

<?php

$image = !empty($user['profile_picture'])

? "../uploads/avatars/".$user['profile_picture']

: "../uploads/default-avatar.png";

?>

<img

src="<?= $image; ?>"

class="rounded-circle border shadow"

width="170"

height="170"

style="object-fit:cover;">

<h3 class="mt-3">

<?= htmlspecialchars($user['fullname']); ?>

</h3>

<p class="text-muted">

@<?= htmlspecialchars($user['username']); ?>

</p>

</div>

<div class="alert alert-danger">

<h5>

<i class="fa-solid fa-triangle-exclamation me-2"></i>

Warning!

</h5>

<p class="mb-0">

You are about to permanently delete this account.

This operation cannot be undone.

</p>

</div>

<div class="row mt-4">

    <div class="col-md-12">

        <table class="table table-bordered">

            <tr>

                <th width="35%">Full Name</th>

                <td><?= htmlspecialchars($user['fullname']); ?></td>

            </tr>

            <tr>

                <th>Username</th>

                <td>@<?= htmlspecialchars($user['username']); ?></td>

            </tr>

            <tr>

                <th>Email</th>

                <td><?= htmlspecialchars($user['email']); ?></td>

            </tr>

            <tr>

                <th>Role</th>

                <td>

                    <?php if($user['role']=="admin"): ?>

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

                <th>Status</th>

                <td>

                    <?php

                    if($user['status']=="active"){

                        echo '<span class="badge bg-success">Active</span>';

                    }elseif($user['status']=="inactive"){

                        echo '<span class="badge bg-secondary">Inactive</span>';

                    }elseif($user['status']=="suspended"){

                        echo '<span class="badge bg-warning text-dark">Suspended</span>';

                    }else{

                        echo '<span class="badge bg-danger">'
                            . htmlspecialchars($user['status']) .
                            '</span>';

                    }

                    ?>

                </td>

            </tr>

            <tr>

                <th>Member Since</th>

                <td>

                    <?= date("F d, Y", strtotime($user['created_at'])); ?>

                </td>

            </tr>

        </table>

    </div>

</div>

<hr>

<h5 class="text-danger">

<i class="fa-solid fa-circle-exclamation me-2"></i>

The following records will also be deleted

</h5>

<ul class="list-group mb-4">

    <li class="list-group-item">

        <i class="fa-solid fa-comments me-2 text-primary"></i>

        All Topics created by this user

    </li>

    <li class="list-group-item">

        <i class="fa-solid fa-reply me-2 text-success"></i>

        All Replies posted by this user

    </li>

    <li class="list-group-item">

        <i class="fa-solid fa-heart me-2 text-danger"></i>

        All Likes associated with this user's replies

    </li>

    <li class="list-group-item">

        <i class="fa-solid fa-bell me-2 text-warning"></i>

        Notifications

    </li>

    <li class="list-group-item">

        <i class="fa-solid fa-clock-rotate-left me-2 text-secondary"></i>

        Activity Logs

    </li>

    <li class="list-group-item">

        <i class="fa-solid fa-image me-2 text-info"></i>

        Profile Picture

    </li>

</ul>

<form method="POST">

    <input
        type="hidden"
        name="user_id"
        value="<?= $userID; ?>">

    <div class="d-flex justify-content-between">

        <a
            href="users.php"
            class="btn btn-secondary">

            <i class="fa-solid fa-arrow-left me-2"></i>

            Cancel

        </a>

        <button
            type="submit"
            name="delete_user"
            class="btn btn-danger">

            <i class="fa-solid fa-trash-can me-2"></i>

            Permanently Delete User

        </button>

    </div>

</form>

<?php

/*
|--------------------------------------------------------------------------
| DELETE USER
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['delete_user'])) {

    try {

        $pdo->beginTransaction();

        /*
        |--------------------------------------------------------------------------
        | DELETE PROFILE PICTURE
        |--------------------------------------------------------------------------
        */

        if (
            !empty($user['profile_picture']) &&
            file_exists("../uploads/avatars/" . $user['profile_picture'])
        ) {

            unlink("../uploads/avatars/" . $user['profile_picture']);

        }

        /*
        |--------------------------------------------------------------------------
        | DELETE LIKES MADE BY USER
        |--------------------------------------------------------------------------
        */

        $stmt = $pdo->prepare("
            DELETE FROM likes
            WHERE user_id = ?
        ");

        $stmt->execute([$userID]);

        /*
        |--------------------------------------------------------------------------
        | DELETE LIKES ON USER REPLIES
        |--------------------------------------------------------------------------
        */

        $stmt = $pdo->prepare("
            DELETE l
            FROM likes l
            INNER JOIN replies r
            ON l.reply_id = r.id
            WHERE r.user_id = ?
        ");

        $stmt->execute([$userID]);

        /*
        |--------------------------------------------------------------------------
        | DELETE USER REPLIES
        |--------------------------------------------------------------------------
        */

        $stmt = $pdo->prepare("
            DELETE FROM replies
            WHERE user_id = ?
        ");

        $stmt->execute([$userID]);

        /*
        |--------------------------------------------------------------------------
        | DELETE USER TOPICS
        |--------------------------------------------------------------------------
        */

        $stmt = $pdo->prepare("
            DELETE FROM topics
            WHERE user_id = ?
        ");

        $stmt->execute([$userID]);

        /*
        |--------------------------------------------------------------------------
        | DELETE NOTIFICATIONS
        |--------------------------------------------------------------------------
        */

        $stmt = $pdo->prepare("
            DELETE FROM notifications
            WHERE user_id = ?
        ");

        $stmt->execute([$userID]);

        /*
        |--------------------------------------------------------------------------
        | DELETE USER ACTIVITY LOGS
        |--------------------------------------------------------------------------
        */

        $stmt = $pdo->prepare("
            DELETE FROM activity_logs
            WHERE user_id = ?
        ");

        $stmt->execute([$userID]);

        /*
        |--------------------------------------------------------------------------
        | DELETE USER ACCOUNT
        |--------------------------------------------------------------------------
        */

        $stmt = $pdo->prepare("
            DELETE FROM users
            WHERE id = ?
        ");

        $stmt->execute([$userID]);

        /*
        |--------------------------------------------------------------------------
        | LOG ADMIN ACTIVITY
        |--------------------------------------------------------------------------
        */

        $activity = "Deleted user account: " . $user['username'];

        $stmt = $pdo->prepare("
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

        $stmt->execute([
            $_SESSION['user_id'],
            $activity,
            $_SERVER['REMOTE_ADDR']
        ]);

        $pdo->commit();

        $_SESSION['success'] = "User account deleted successfully.";

    } catch (PDOException $e) {

        $pdo->rollBack();

        $_SESSION['error'] = "Failed to delete user account.";

    }

    header("Location: users.php");
    exit();

}

?>

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