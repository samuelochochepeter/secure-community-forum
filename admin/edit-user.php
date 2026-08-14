<?php
session_start();

require_once '../config/config.php';
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SESSION['role'] != 'admin') {
    $_SESSION['error'] = "Access Denied!";
    header("Location: ../dashboard/index.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| GET USER ID
|--------------------------------------------------------------------------
*/

if (!isset($_GET['id'])) {
    $_SESSION['error'] = "Invalid User.";
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
WHERE id=?
LIMIT 1
");

$stmt->execute([$userID]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {

    $_SESSION['error']="User not found.";

    header("Location: users.php");

    exit();

}

/*
|--------------------------------------------------------------------------
| USER STATISTICS
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
SELECT COUNT(*)
FROM topics
WHERE user_id=?
");

$stmt->execute([$userID]);

$totalTopics = $stmt->fetchColumn();

$stmt = $pdo->prepare("
SELECT COUNT(*)
FROM replies
WHERE user_id=?
");

$stmt->execute([$userID]);

$totalReplies = $stmt->fetchColumn();

$stmt = $pdo->prepare("
SELECT COUNT(*)
FROM likes l
INNER JOIN replies r
ON l.reply_id=r.id
WHERE r.user_id=?
");

$stmt->execute([$userID]);

$totalLikes = $stmt->fetchColumn();

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1.0">

<title>

Edit User

</title>

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

<div class="row mb-4">

<div class="col-md-8">

<h2 class="fw-bold">

<i class="fa-solid fa-user-pen text-warning me-2"></i>

Edit User

</h2>

<p class="text-muted">

Update user information, account status and permissions.

</p>

</div>

<div class="col-md-4 text-end">

<a
href="user-details.php?id=<?= $userID ?>"
class="btn btn-secondary">

<i class="fa-solid fa-arrow-left me-2"></i>

Back

</a>

</div>

</div>

<div class="row">

<!-- Left Card -->

<div class="col-lg-4">

<div class="card shadow border-0 mb-4">

<div class="card-body text-center">

<?php

$photo = !empty($user['profile_picture'])

? "../uploads/avatars/".$user['profile_picture']

: "../uploads/default-avatar.png";

?>

<img

src="<?= $photo ?>"

class="rounded-circle border shadow mb-3"

width="170"

height="170"

style="object-fit:cover;">

<h4>

<?= htmlspecialchars($user['fullname']) ?>

</h4>

<p class="text-muted">

@<?= htmlspecialchars($user['username']) ?>

</p>

<hr>

<div class="row text-center">

<div class="col-4">

<h4>

<?= $totalTopics ?>

</h4>

<small>

Topics

</small>

</div>

<div class="col-4">

<h4>

<?= $totalReplies ?>

</h4>

<small>

Replies

</small>

</div>

<div class="col-4">

<h4>

<?= $totalLikes ?>

</h4>

<small>

Likes

</small>

</div>

</div>

<hr>

<p>

<strong>Email</strong>

<br>

<?= htmlspecialchars($user['email']) ?>

</p>

<p>

<strong>Status</strong>

<br>

<?php

if($user['status']=="active"){

echo '<span class="badge bg-success">Active</span>';

}

elseif($user['status']=="suspended"){

echo '<span class="badge bg-warning text-dark">Suspended</span>';

}

else{

echo '<span class="badge bg-danger">'.$user['status'].'</span>';

}

?>

</p>

<p>

<strong>Role</strong>

<br>

<?php

if($user['role']=="admin"){

echo '<span class="badge bg-danger">Administrator</span>';

}else{

echo '<span class="badge bg-primary">Member</span>';

}

?>

</p>

</div>

</div>

</div>

<!-- Right Card -->

<div class="col-lg-8">

<div class="card shadow border-0">

<div class="card-header bg-warning">

<h5 class="mb-0 text-dark">

<i class="fa-solid fa-user-gear me-2"></i>

Edit User Information

</h5>

</div>

<div class="card-body">

<form

action="update-user.php"

method="POST"

enctype="multipart/form-data">

<input

type="hidden"

name="user_id"

value="<?= $user['id'] ?>">

<!-- FORM STARTS HERE -->
                         <div class="row">

                            <!-- Full Name -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    Full Name
                                </label>

                                <input
                                    type="text"
                                    name="fullname"
                                    class="form-control"
                                    value="<?= htmlspecialchars($user['fullname']) ?>"
                                    required>
                            </div>

                            <!-- Username -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    Username
                                </label>

                                <input
                                    type="text"
                                    name="username"
                                    class="form-control"
                                    value="<?= htmlspecialchars($user['username']) ?>"
                                    required>
                            </div>

                            <!-- Email -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    Email Address
                                </label>

                                <input
                                    type="email"
                                    name="email"
                                    class="form-control"
                                    value="<?= htmlspecialchars($user['email']) ?>"
                                    required>
                            </div>

                            <!-- Role -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    User Role
                                </label>

                                <select
                                    name="role"
                                    class="form-select">

                                    <option value="member"
                                        <?= ($user['role']=="member") ? "selected" : "" ?>>
                                        Member
                                    </option>

                                    <option value="admin"
                                        <?= ($user['role']=="admin") ? "selected" : "" ?>>
                                        Administrator
                                    </option>

                                </select>
                            </div>

                            <!-- Status -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    Account Status
                                </label>

                                <select
                                    name="status"
                                    class="form-select">

                                    <option value="active"
                                        <?= ($user['status']=="active") ? "selected" : "" ?>>
                                        Active
                                    </option>

                                    <option value="inactive"
                                        <?= ($user['status']=="inactive") ? "selected" : "" ?>>
                                        Inactive
                                    </option>

                                    <option value="suspended"
                                        <?= ($user['status']=="suspended") ? "selected" : "" ?>>
                                        Suspended
                                    </option>

                                </select>
                            </div>

                            <!-- Email Verification -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    Email Verification
                                </label>

                                <select
                                    name="email_verified"
                                    class="form-select">

                                    <option value="1"
                                        <?= ($user['email_verified']==1) ? "selected" : "" ?>>
                                        Verified
                                    </option>

                                    <option value="0"
                                        <?= ($user['email_verified']==0) ? "selected" : "" ?>>
                                        Not Verified
                                    </option>

                                </select>
                            </div>

                            <!-- Bio -->
                            <div class="col-12 mb-3">

                                <label class="form-label fw-semibold">
                                    Biography
                                </label>

                                <textarea
                                    name="bio"
                                    rows="5"
                                    class="form-control"
                                    placeholder="Write something about this user..."><?= htmlspecialchars($user['bio']) ?></textarea>

                            </div>

                            <!-- Upload Avatar -->
                            <div class="col-md-6 mb-4">

                                <label class="form-label fw-semibold">
                                    Profile Picture
                                </label>

                                <input
                                    type="file"
                                    name="profile_picture"
                                    class="form-control"
                                    accept=".jpg,.jpeg,.png,.gif,.webp">

                                <small class="text-muted">
                                    Leave empty if you don't want to change the avatar.
                                </small>

                            </div>

                            <!-- New Password -->
                            <div class="col-md-6 mb-4">

                                <label class="form-label fw-semibold">
                                    Reset Password
                                </label>

                                <input
                                    type="password"
                                    name="password"
                                    class="form-control"
                                    placeholder="Leave empty to keep current password">

                                <small class="text-muted">
                                    Only enter a password if you want to reset it.
                                </small>

                            </div>

                        </div>

                        <hr>

                        <div class="d-flex justify-content-between">

                            <a
                                href="users.php"
                                class="btn btn-secondary">

                                <i class="fa-solid fa-arrow-left me-2"></i>

                                Cancel

                            </a>

                            <button
                                type="submit"
                                class="btn btn-warning">

                                <i class="fa-solid fa-floppy-disk me-2"></i>

                                Save Changes

                            </button>

                        </div>

                                            </form>

                </div>

            </div>

            <!-- Account Information -->

            <div class="card shadow border-0 mt-4">

                <div class="card-header bg-light">

                    <h5 class="mb-0">

                        <i class="fa-solid fa-circle-info me-2"></i>

                        Account Information

                    </h5>

                </div>

                <div class="card-body">

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <strong>User ID</strong>

                            <p class="text-muted mb-0">

                                #<?= $user['id']; ?>

                            </p>

                        </div>

                        <div class="col-md-6 mb-3">

                            <strong>Member Since</strong>

                            <p class="text-muted mb-0">

                                <?= date("F d, Y h:i A", strtotime($user['created_at'])); ?>

                            </p>

                        </div>

                        <div class="col-md-6 mb-3">

                            <strong>Last Login</strong>

                            <p class="text-muted mb-0">

                                <?= !empty($user['last_login'])
                                    ? date("F d, Y h:i A", strtotime($user['last_login']))
                                    : "Never Logged In"; ?>

                            </p>

                        </div>

                        <div class="col-md-6 mb-3">

                            <strong>Last Updated</strong>

                            <p class="text-muted mb-0">

                                <?= !empty($user['updated_at'])
                                    ? date("F d, Y h:i A", strtotime($user['updated_at']))
                                    : "Not Available"; ?>

                            </p>

                        </div>

                    </div>

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

<script>

document.addEventListener("DOMContentLoaded", function () {

    const avatarInput = document.querySelector('input[name="profile_picture"]');

    if (avatarInput) {

        avatarInput.addEventListener("change", function () {

            if (this.files.length > 0) {

                const file = this.files[0];

                const allowed = [
                    "image/jpeg",
                    "image/png",
                    "image/jpg",
                    "image/webp",
                    "image/gif"
                ];

                if (!allowed.includes(file.type)) {

                    alert("Only JPG, JPEG, PNG, WEBP and GIF images are allowed.");

                    this.value = "";

                    return;

                }

                if (file.size > 2 * 1024 * 1024) {

                    alert("Maximum image size is 2MB.");

                    this.value = "";

                    return;

                }

            }

        });

    }

});

</script>

</body>

</html>