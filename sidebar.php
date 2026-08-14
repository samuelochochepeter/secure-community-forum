<?php

$currentPage = basename($_SERVER['PHP_SELF']);

function activeMenu($pages)
{
    global $currentPage;

    return in_array($currentPage, (array)$pages)
        ? 'active'
        : '';
}

?>

<div class="sidebar bg-dark text-white">

    <!-- Logo -->

    <div class="sidebar-header text-center py-4 border-bottom">

        <i class="fa-solid fa-comments fa-3x text-primary mb-3"></i>

        <h4 class="fw-bold mb-1">

            <?= SITE_NAME; ?>

        </h4>

        <small class="text-light">

            Secure Community Forum

        </small>

    </div>

    <!-- Logged-in User -->

    <div class="text-center py-3 border-bottom">

        <h6 class="mb-1">

            <?= htmlspecialchars($_SESSION['fullname']); ?>

        </h6>

        <small class="text-light">

            <?= ucfirst($_SESSION['role'] ?? 'Member'); ?>

        </small>

    </div>

    <!-- Navigation -->

    <ul class="nav flex-column py-3">

        <!-- Dashboard -->

        <li class="nav-item">

            <a href="<?= BASE_URL ?>dashboard/index.php"
               class="nav-link text-white <?= activeMenu(['index.php']); ?>">

                <i class="fa-solid fa-gauge-high me-2"></i>

                Dashboard

            </a>

        </li>

        <!-- Categories -->

        <li class="nav-item">

            <a href="<?= BASE_URL ?>forum/categories.php"
               class="nav-link text-white <?= activeMenu(['categories.php']); ?>">

                <i class="fa-solid fa-layer-group me-2"></i>

                Categories

            </a>

        </li>

        <!-- Create Discussion -->

        <li class="nav-item">

            <a href="<?= BASE_URL ?>discussion/create-discussion.php"
               class="nav-link text-white <?= activeMenu(['create-discussion.php']); ?>">

                <i class="fa-solid fa-pen-to-square me-2"></i>

                Create Discussion

            </a>

        </li>

        <!-- My Discussions -->

        <li class="nav-item">

            <a href="<?= BASE_URL ?>discussion/my-discussions.php"
               class="nav-link text-white <?= activeMenu(['my-discussions.php']); ?>">

                <i class="fa-solid fa-comments me-2"></i>

                My Discussions

            </a>

        </li>

        <!-- Profile -->

        <li class="nav-item">

            <a href="<?= BASE_URL ?>profile/index.php"
               class="nav-link text-white <?= activeMenu(['index.php']); ?>">

                <i class="fa-solid fa-user me-2"></i>

                Profile

            </a>

        </li>

        <!-- Settings -->

        <li class="nav-item">

            <a href="<?= BASE_URL ?>settings/index.php"
               class="nav-link text-white <?= activeMenu(['index.php']); ?>">

                <i class="fa-solid fa-gear me-2"></i>

                Settings

            </a>

        </li>

        <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>

        <!-- Admin Panel -->

        <li class="nav-item">

            <a href="<?= BASE_URL ?>admin/dashboard.php"
               class="nav-link text-warning">

                <i class="fa-solid fa-user-shield me-2"></i>

                Admin Panel

            </a>

        </li>

        <?php endif; ?>

        <!-- Logout -->

        <li class="nav-item mt-5">

            <a href="<?= BASE_URL ?>authentication/logout.php"
               class="nav-link text-danger">

                <i class="fa-solid fa-right-from-bracket me-2"></i>

                Logout

            </a>

        </li>

    </ul>

</div>