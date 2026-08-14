<?php

require_once '../config/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

require_once '../helpers/dashboard-stats.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard | <?= SITE_NAME; ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <link rel="stylesheet"
          href="../assets/css/style.css">

</head>

<body class="bg-light">

<div class="d-flex">

    <?php include 'sidebar.php'; ?>

    <div class="flex-grow-1">

        <?php include 'topbar.php'; ?>

        <div class="container-fluid p-4">

            <!-- Welcome Card -->

            <div class="card shadow border-0 mb-4">

                <div class="card-body">

                    <h2 class="fw-bold">

                        Welcome,

                        <?= htmlspecialchars($_SESSION['fullname']); ?>

                        👋

                    </h2>

                    <p class="text-muted mb-0">

                        Manage your discussions and stay connected with the community.

                    </p>

                </div>

            </div>

            <!-- Statistics -->

            <div class="row g-4">

                <div class="col-lg-3 col-md-6">

                    <div class="card shadow-sm border-0 text-center">

                        <div class="card-body">

                            <i class="fa-solid fa-comments fa-2x text-primary mb-3"></i>

                            <h1><?= $totalDiscussions; ?></h1>

                            <p class="text-muted mb-0">

                                Total Discussions

                            </p>

                        </div>

                    </div>

                </div>

                <div class="col-lg-3 col-md-6">

                    <div class="card shadow-sm border-0 text-center">

                        <div class="card-body">

                            <i class="fa-solid fa-reply fa-2x text-success mb-3"></i>

                            <h1><?= $totalReplies; ?></h1>

                            <p class="text-muted mb-0">

                                Total Replies

                            </p>

                        </div>

                    </div>

                </div>

                <div class="col-lg-3 col-md-6">

                    <div class="card shadow-sm border-0 text-center">

                        <div class="card-body">

                            <i class="fa-solid fa-heart fa-2x text-danger mb-3"></i>

                            <h1><?= $totalLikes; ?></h1>

                            <p class="text-muted mb-0">

                                Total Likes

                            </p>

                        </div>

                    </div>

                </div>

                <div class="col-lg-3 col-md-6">

                    <div class="card shadow-sm border-0 text-center">

                        <div class="card-body">

                            <i class="fa-solid fa-layer-group fa-2x text-warning mb-3"></i>

                            <h1><?= $totalCategories; ?></h1>

                            <p class="text-muted mb-0">

                                Categories

                            </p>

                        </div>

                    </div>

                </div>

            </div>

            <!-- Recent Discussions -->

            <div class="card shadow border-0 mt-4">

                <div class="card-header bg-primary text-white">

                    <h5 class="mb-0">

                        <i class="fa-solid fa-clock-rotate-left me-2"></i>

                        Recent Discussions

                    </h5>

                </div>

                <div class="card-body">

                    <?php if(count($recentDiscussions) > 0): ?>

                        <div class="table-responsive">

                            <table class="table table-hover align-middle">

                                <thead>

                                <tr>

                                    <th>Title</th>

                                    <th>Category</th>

                                    <th>Author</th>

                                    <th>Date</th>

                                    <th class="text-center">Action</th>

                                </tr>

                                </thead>

                                <tbody>

                                <?php foreach($recentDiscussions as $discussion): ?>

                                    <tr>

                                        <td>

                                            <a
                                                href="../discussion/view-discussion.php?id=<?= $discussion['id']; ?>"
                                                class="text-decoration-none fw-semibold">

                                                <?= htmlspecialchars($discussion['title']); ?>

                                            </a>

                                        </td>

                                        <td>

                                            <span class="badge bg-primary">

                                                <?= htmlspecialchars($discussion['name']); ?>

                                            </span>

                                        </td>

                                        <td>

                                            <?= htmlspecialchars($discussion['fullname']); ?>

                                        </td>

                                        <td>

                                            <?= date("d M Y", strtotime($discussion['created_at'])); ?>

                                        </td>

                                        <td class="text-center">

                                            <a
                                                href="../discussion/view-discussion.php?id=<?= $discussion['id']; ?>"
                                                class="btn btn-sm btn-outline-primary">

                                                <i class="fa-solid fa-eye me-1"></i>

                                                View

                                            </a>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                                </tbody>

                            </table>

                        </div>

                    <?php else: ?>

                        <div class="text-center py-5">

                            <i class="fa-solid fa-comments fa-4x text-secondary mb-3"></i>

                            <h4>No Discussions Yet</h4>

                            <p class="text-muted">

                                No discussions have been created yet.

                            </p>

                            <a href="../discussion/create-discussion.php" class="btn btn-primary">

                                <i class="fa-solid fa-plus me-2"></i>

                                Create First Discussion

                            </a>

                        </div>

                    <?php endif; ?>

                </div>

            </div>

            <?php include 'footer.php'; ?>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>