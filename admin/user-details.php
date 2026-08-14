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
| CHECK USER ID
|--------------------------------------------------------------------------
*/

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "Invalid User!";
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
    $_SESSION['error'] = "User not found!";
    header("Location: users.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| USER STATISTICS
|--------------------------------------------------------------------------
*/

// Total Topics

$stmt = $pdo->prepare("
SELECT COUNT(*)
FROM topics
WHERE user_id=?
");

$stmt->execute([$userID]);

$totalTopics = $stmt->fetchColumn();


// Total Replies

$stmt = $pdo->prepare("
SELECT COUNT(*)
FROM replies
WHERE user_id=?
");

$stmt->execute([$userID]);

$totalReplies = $stmt->fetchColumn();


// Total Likes Received

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

Admin | User Details

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

<!-- Page Heading -->

<div class="row mb-4">

<div class="col-md-8">

<h2 class="fw-bold">

<i class="fa-solid fa-user text-primary me-2"></i>

User Details

</h2>

<p class="text-muted">

View complete information about this registered user.

</p>

</div>

<div class="col-md-4 text-end">

<a
href="users.php"
class="btn btn-secondary">

<i class="fa-solid fa-arrow-left me-2"></i>

Back to Users

</a>

</div>

</div>

<!-- Profile Card -->

<div class="card shadow border-0 mb-4">

<div class="card-body">

<div class="row align-items-center">

<div class="col-md-3 text-center">

<?php

$photo = !empty($user['profile_picture'])
    ? "../uploads/avatars/".$user['profile_picture']
    : "../uploads/default-avatar.png";

?>

<img

src="<?= htmlspecialchars($photo); ?>"

class="rounded-circle border shadow"

width="170"

height="170"

style="object-fit:cover;">

</div>

<div class="col-md-9">

<h3 class="fw-bold">

<?= htmlspecialchars($user['fullname']); ?>

</h3>

<p class="text-muted mb-3">

@<?= htmlspecialchars($user['username']); ?>

</p>

<div class="row">

<div class="col-md-6 mb-3">

<strong>Email</strong>

<br>

<?= htmlspecialchars($user['email']); ?>

</div>

<div class="col-md-6 mb-3">

<strong>Role</strong>

<br>

<?php if($user['role']=="admin"): ?>

<span class="badge bg-danger">

Administrator

</span>

<?php else: ?>

<span class="badge bg-primary">

Member

</span>

<?php endif; ?>

</div>

<div class="col-md-6 mb-3">

<strong>Status</strong>

<br>

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

</div>

<div class="col-md-6 mb-3">

<strong>Email Verification</strong>

<br>

<?php if($user['email_verified']): ?>

<span class="badge bg-success">

Verified

</span>

<?php else: ?>

<span class="badge bg-warning text-dark">

Not Verified

</span>

<?php endif; ?>

</div>

<div class="col-md-6 mb-3">

<strong>Last Login</strong>

<br>

<?= !empty($user['last_login'])
? date('d M Y h:i A', strtotime($user['last_login']))
: 'Never Logged In'; ?>

</div>

<div class="col-md-6 mb-3">

<strong>Member Since</strong>

<br>

<?= date('d M Y', strtotime($user['created_at'])); ?>

</div>

<div class="col-12">

<strong>Bio</strong>

<p class="mt-2 text-muted">

<?= !empty($user['bio'])
? nl2br(htmlspecialchars($user['bio']))
: 'No bio available.'; ?>

</p>

</div>

</div>

</div>

</div>

</div>

</div>

<!-- Statistics -->

<div class="row mb-4">

<div class="col-md-4">

<div class="card border-0 shadow">

<div class="card-body text-center">

<i class="fa-solid fa-comments fa-3x text-primary mb-3"></i>

<h2>

<?= $totalTopics; ?>

</h2>

<p class="text-muted">

Topics Created

</p>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card border-0 shadow">

<div class="card-body text-center">

<i class="fa-solid fa-reply fa-3x text-success mb-3"></i>

<h2>

<?= $totalReplies; ?>

</h2>

<p class="text-muted">

Replies Posted

</p>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card border-0 shadow">

<div class="card-body text-center">

<i class="fa-solid fa-heart fa-3x text-danger mb-3"></i>

<h2>

<?= $totalLikes; ?>

</h2>

<p class="text-muted">

Likes Received

</p>

</div>

</div>

</div>

</div>

<!-- Recent Topics Starts Here -->

<!-- Recent Topics -->

<?php

$stmt = $pdo->prepare("
SELECT
topics.id,
topics.title,
topics.views,
topics.status,
topics.created_at,
categories.name AS category
FROM topics
LEFT JOIN categories
ON topics.category_id = categories.id
WHERE topics.user_id = ?
ORDER BY topics.created_at DESC
LIMIT 5
");

$stmt->execute([$userID]);

$recentTopics = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="card shadow border-0 mb-4">

    <div class="card-header bg-primary text-white">

        <h5 class="mb-0">

            <i class="fa-solid fa-comments me-2"></i>

            Recent Topics

        </h5>

    </div>

    <div class="card-body p-0">

        <?php if(count($recentTopics) > 0): ?>

        <div class="table-responsive">

            <table class="table table-hover mb-0">

                <thead class="table-light">

                <tr>

                    <th>Title</th>

                    <th>Category</th>

                    <th>Views</th>

                    <th>Status</th>

                    <th>Date</th>

                </tr>

                </thead>

                <tbody>

                <?php foreach($recentTopics as $topic): ?>

                <tr>

                    <td>

                        <a href="../forum/view-topic.php?id=<?= $topic['id']; ?>">

                            <?= htmlspecialchars($topic['title']); ?>

                        </a>

                    </td>

                    <td>

                        <?= htmlspecialchars($topic['category']); ?>

                    </td>

                    <td>

                        <?= $topic['views']; ?>

                    </td>

                    <td>

                        <?php if($topic['status']=="open"): ?>

                            <span class="badge bg-success">

                                Open

                            </span>

                        <?php else: ?>

                            <span class="badge bg-secondary">

                                Closed

                            </span>

                        <?php endif; ?>

                    </td>

                    <td>

                        <?= date('d M Y', strtotime($topic['created_at'])); ?>

                    </td>

                </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        </div>

        <?php else: ?>

        <div class="text-center py-5">

            <i class="fa-solid fa-comments fa-3x text-muted mb-3"></i>

            <h5>No Topics Created</h5>

        </div>

        <?php endif; ?>

    </div>

</div>

<!-- Recent Replies -->

<?php

$stmt = $pdo->prepare("
SELECT
replies.reply,
replies.created_at,
topics.title
FROM replies
LEFT JOIN topics
ON replies.topic_id = topics.id
WHERE replies.user_id=?
ORDER BY replies.created_at DESC
LIMIT 5
");

$stmt->execute([$userID]);

$recentReplies = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="card shadow border-0 mb-4">

    <div class="card-header bg-success text-white">

        <h5 class="mb-0">

            <i class="fa-solid fa-reply me-2"></i>

            Recent Replies

        </h5>

    </div>

    <div class="card-body p-0">

        <?php if(count($recentReplies)>0): ?>

        <div class="table-responsive">

            <table class="table table-striped mb-0">

                <thead class="table-light">

                <tr>

                    <th>Topic</th>

                    <th>Reply</th>

                    <th>Date</th>

                </tr>

                </thead>

                <tbody>

                <?php foreach($recentReplies as $reply): ?>

                <tr>

                    <td>

                        <?= htmlspecialchars($reply['title']); ?>

                    </td>

                    <td>

                        <?= htmlspecialchars(substr($reply['reply'],0,80)); ?>...

                    </td>

                    <td>

                        <?= date('d M Y', strtotime($reply['created_at'])); ?>

                    </td>

                </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        </div>

        <?php else: ?>

        <div class="text-center py-5">

            <i class="fa-solid fa-reply fa-3x text-muted mb-3"></i>

            <h5>No Replies Posted</h5>

        </div>

        <?php endif; ?>

    </div>

</div>

<!-- Administrator Actions -->

<div class="card shadow border-0 mb-4">

    <div class="card-header bg-dark text-white">

        <h5 class="mb-0">

            <i class="fa-solid fa-gears me-2"></i>

            Administrator Actions

        </h5>

    </div>

    <div class="card-body">

        <div class="d-flex flex-wrap gap-3">

            <a
                href="edit-user.php?id=<?= $user['id']; ?>"
                class="btn btn-warning">

                <i class="fa-solid fa-pen me-2"></i>

                Edit User

            </a>

            <a
                href="suspend-user.php?id=<?= $user['id']; ?>"
                class="btn btn-secondary">

                <i class="fa-solid fa-user-lock me-2"></i>

                Suspend User

            </a>

            <a
                href="delete-user.php?id=<?= $user['id']; ?>"
                class="btn btn-danger"
                onclick="return confirm('Delete this user permanently?');">

                <i class="fa-solid fa-trash me-2"></i>

                Delete User

            </a>

            <a
                href="users.php"
                class="btn btn-primary">

                <i class="fa-solid fa-arrow-left me-2"></i>

                Back to Users

            </a>

        </div>

    </div>

</div>

<!-- Footer Starts Here -->

<!-- Footer -->

<?php include 'includes/footer.php'; ?>

</div>

<!-- End Container -->

</div>

<!-- End Content -->

</div>

<!-- End Wrapper -->

<!-- Bootstrap JavaScript -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- FontAwesome -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/js/all.min.js"></script>

<!-- Optional Custom JavaScript -->

<script src="../assets/js/app.js"></script>

<script>

// Auto-hide alerts after 5 seconds

setTimeout(function(){

    const alerts = document.querySelectorAll('.alert');

    alerts.forEach(function(alert){

        let bsAlert = bootstrap.Alert.getOrCreateInstance(alert);

        bsAlert.close();

    });

}, 5000);


// Enable Bootstrap Tooltips

const tooltipTriggerList = document.querySelectorAll('[title]');

tooltipTriggerList.forEach(function(element){

    new bootstrap.Tooltip(element);

});

</script>

</body>

</html>