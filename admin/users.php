<?php
session_start();

require_once '../config/config.php';
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = "Access Denied!";
    header("Location: ../dashboard/index.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| SEARCH
|--------------------------------------------------------------------------
*/

$search = '';

if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}

/*
|--------------------------------------------------------------------------
| PAGINATION
|--------------------------------------------------------------------------
*/

$limit = 10;

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if ($page < 1) {
    $page = 1;
}

$offset = ($page - 1) * $limit;

/*
|--------------------------------------------------------------------------
| COUNT USERS
|--------------------------------------------------------------------------
*/

$countQuery = $pdo->prepare("
SELECT COUNT(*)
FROM users
WHERE
fullname LIKE :search
OR username LIKE :search
OR email LIKE :search
");

$countQuery->execute([
    ':search' => "%{$search}%"
]);

$totalUsers = $countQuery->fetchColumn();

$totalPages = ceil($totalUsers / $limit);

/*
|--------------------------------------------------------------------------
| FETCH USERS
|--------------------------------------------------------------------------
*/

$userQuery = $pdo->prepare("
SELECT
id,
fullname,
username,
email,
role,
status,
email_verified,
profile_picture,
created_at
FROM users
WHERE
fullname LIKE :search
OR username LIKE :search
OR email LIKE :search
ORDER BY created_at DESC
LIMIT {$limit}
OFFSET {$offset}
");

$userQuery->execute([
    ':search' => "%{$search}%"
]);

$users = $userQuery->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1.0">

<title>

Admin | User Management

</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<link rel="stylesheet" href="../assets/css/style.css">

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

<i class="fa-solid fa-users text-primary me-2"></i>

User Management

</h2>

<p class="text-muted">

Manage registered users, update accounts, suspend members and control access.

</p>

</div>

<div class="col-md-4 text-end">

<a
href="../register.php"
class="btn btn-success">

<i class="fa-solid fa-user-plus me-2"></i>

Add New User

</a>

</div>

</div>

<!-- Alert Messages -->

<?php if(isset($_SESSION['success'])): ?>

<div class="alert alert-success alert-dismissible fade show">

<?= $_SESSION['success']; ?>

<button
type="button"
class="btn-close"
data-bs-dismiss="alert"></button>

</div>

<?php unset($_SESSION['success']); ?>

<?php endif; ?>

<?php if(isset($_SESSION['error'])): ?>

<div class="alert alert-danger alert-dismissible fade show">

<?= $_SESSION['error']; ?>

<button
type="button"
class="btn-close"
data-bs-dismiss="alert"></button>

</div>

<?php unset($_SESSION['error']); ?>

<?php endif; ?>

<!-- Statistics Cards -->

<div class="row mb-4">

<div class="col-lg-3 col-md-6 mb-3">

<div class="card border-0 shadow">

<div class="card-body">

<div class="d-flex justify-content-between align-items-center">

<div>

<h6 class="text-muted">

Total Users

</h6>

<h2>

<?= $totalUsers ?>

</h2>

</div>

<div>

<i class="fa-solid fa-users fa-3x text-primary"></i>

</div>

</div>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6 mb-3">

<div class="card border-0 shadow">

<div class="card-body">

<div class="d-flex justify-content-between align-items-center">

<div>

<h6 class="text-muted">

Administrators

</h6>

<h2>

<?php

$stmt = $pdo->query("
SELECT COUNT(*)
FROM users
WHERE role='admin'
");

echo $stmt->fetchColumn();

?>

</h2>

</div>

<div>

<i class="fa-solid fa-user-shield fa-3x text-danger"></i>

</div>

</div>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6 mb-3">

<div class="card border-0 shadow">

<div class="card-body">

<div class="d-flex justify-content-between align-items-center">

<div>

<h6 class="text-muted">

Verified Users

</h6>

<h2>

<?php

$stmt = $pdo->query("
SELECT COUNT(*)
FROM users
WHERE email_verified=1
");

echo $stmt->fetchColumn();

?>

</h2>

</div>

<div>

<i class="fa-solid fa-circle-check fa-3x text-success"></i>

</div>

</div>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6 mb-3">

<div class="card border-0 shadow">

<div class="card-body">

<div class="d-flex justify-content-between align-items-center">

<div>

<h6 class="text-muted">

Inactive Users

</h6>

<h2>

<?php

$stmt = $pdo->query("
SELECT COUNT(*)
FROM users
WHERE status!='active'
");

echo $stmt->fetchColumn();

?>

</h2>

</div>

<div>

<i class="fa-solid fa-user-slash fa-3x text-warning"></i>

</div>

</div>

</div>

</div>

</div>

</div>

<!-- Search Card -->

<div class="card shadow border-0 mb-4">

<div class="card-body">

<form method="GET">

<div class="row">

<div class="col-md-10">

<input

type="text"

name="search"

class="form-control"

placeholder="Search by Name, Username or Email..."

value="<?= htmlspecialchars($search); ?>">

</div>

<div class="col-md-2">

<button
class="btn btn-primary w-100">

<i class="fa-solid fa-search me-2"></i>

Search

</button>

</div>

</div>

</form>

</div>

</div>

<!-- User Table Starts Here -->

<div class="card shadow border-0">

    <div class="card-header bg-primary text-white">

        <h5 class="mb-0">

            <i class="fa-solid fa-users me-2"></i>

            Registered Users

        </h5>

    </div>

    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-hover table-striped align-middle mb-0">

                <thead class="table-dark">

                    <tr>

                        <th>ID</th>

                        <th>Photo</th>

                        <th>Full Name</th>

                        <th>Username</th>

                        <th>Email</th>

                        <th>Role</th>

                        <th>Status</th>

                        <th>Verified</th>

                        <th>Registered</th>

                        <th width="260">Actions</th>

                    </tr>

                </thead>

                <tbody>

                <?php if(count($users) > 0): ?>

                    <?php foreach($users as $user): ?>

                    <tr>

                        <td>

                            <?= $user['id']; ?>

                        </td>

                        <td>

                            <?php

                            $photo = !empty($user['profile_picture'])
                                ? "../uploads/avatars/".$user['profile_picture']
                                : "../uploads/default-avatar.png";

                            ?>

                            <img

                                src="<?= htmlspecialchars($photo); ?>"

                                width="55"

                                height="55"

                                class="rounded-circle border"

                                style="object-fit:cover;">

                        </td>

                        <td>

                            <strong>

                                <?= htmlspecialchars($user['fullname']); ?>

                            </strong>

                        </td>

                        <td>

                            <?= htmlspecialchars($user['username']); ?>

                        </td>

                        <td>

                            <?= htmlspecialchars($user['email']); ?>

                        </td>

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

                        <td>

                            <?php

                            switch($user['status']){

                                case 'active':

                                    echo '<span class="badge bg-success">Active</span>';

                                    break;

                                case 'suspended':

                                    echo '<span class="badge bg-warning text-dark">Suspended</span>';

                                    break;

                                case 'blocked':

                                    echo '<span class="badge bg-danger">Blocked</span>';

                                    break;

                                default:

                                    echo '<span class="badge bg-secondary">'
                                    .htmlspecialchars($user['status']).
                                    '</span>';

                            }

                            ?>

                        </td>

                        <td>

                            <?php if($user['email_verified']): ?>

                                <span class="badge bg-success">

                                    <i class="fa-solid fa-check"></i>

                                </span>

                            <?php else: ?>

                                <span class="badge bg-warning text-dark">

                                    <i class="fa-solid fa-xmark"></i>

                                </span>

                            <?php endif; ?>

                        </td>

                        <td>

                            <?= date("d M Y", strtotime($user['created_at'])); ?>

                        </td>

                        <td>

                            <div class="btn-group btn-group-sm">

                                <a

                                href="user-details.php?id=<?= $user['id']; ?>"

                                class="btn btn-info text-white"

                                title="View">

                                <i class="fa-solid fa-eye"></i>

                                </a>

                                <a

                                href="edit-user.php?id=<?= $user['id']; ?>"

                                class="btn btn-warning"

                                title="Edit">

                                <i class="fa-solid fa-pen"></i>

                                </a>

                                <a

                                href="suspend-user.php?id=<?= $user['id']; ?>"

                                class="btn btn-secondary"

                                title="Suspend">

                                <i class="fa-solid fa-user-lock"></i>

                                </a>

                                <a

                                href="delete-user.php?id=<?= $user['id']; ?>"

                                class="btn btn-danger"

                                title="Delete"

                                onclick="return confirm('Are you sure you want to delete this user?');">

                                <i class="fa-solid fa-trash"></i>

                                </a>

                            </div>

                        </td>

                    </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>

                        <td colspan="10">

                            <div class="text-center py-5">

                                <i class="fa-solid fa-users-slash fa-4x text-muted mb-3"></i>

                                <h4>

                                    No Users Found

                                </h4>

                                <p class="text-muted">

                                    There are no users matching your search.

                                </p>

                            </div>

                        </td>

                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<!-- Pagination Starts Below -->

<!-- Pagination -->

<?php if($totalPages > 1): ?>

<div class="card border-0 shadow mt-4">

    <div class="card-body">

        <div class="row align-items-center">

            <div class="col-md-6">

                <?php

                $start = ($page - 1) * $limit + 1;

                $end = min($page * $limit, $totalUsers);

                ?>

                <small class="text-muted">

                    Showing

                    <strong><?= $start; ?></strong>

                    to

                    <strong><?= $end; ?></strong>

                    of

                    <strong><?= $totalUsers; ?></strong>

                    users

                </small>

            </div>

            <div class="col-md-6">

                <nav class="float-md-end">

                    <ul class="pagination mb-0">

                        <!-- Previous -->

                        <?php if($page > 1): ?>

                        <li class="page-item">

                            <a

                            class="page-link"

                            href="?page=<?= $page-1; ?>&search=<?= urlencode($search); ?>">

                                Previous

                            </a>

                        </li>

                        <?php endif; ?>

                        <!-- Page Numbers -->

                        <?php for($i=1; $i <= $totalPages; $i++): ?>

                        <li class="page-item <?= ($page==$i)?'active':''; ?>">

                            <a

                            class="page-link"

                            href="?page=<?= $i; ?>&search=<?= urlencode($search); ?>">

                                <?= $i; ?>

                            </a>

                        </li>

                        <?php endfor; ?>

                        <!-- Next -->

                        <?php if($page < $totalPages): ?>

                        <li class="page-item">

                            <a

                            class="page-link"

                            href="?page=<?= $page+1; ?>&search=<?= urlencode($search); ?>">

                                Next

                            </a>

                        </li>

                        <?php endif; ?>

                    </ul>

                </nav>

            </div>

        </div>

    </div>

</div>

<?php endif; ?>

</div>

<!-- End Container -->

<?php include 'includes/footer.php'; ?>

</div>

<!-- End Content -->

</div>

<!-- End Wrapper -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>