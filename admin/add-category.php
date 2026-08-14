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

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Add Category</title>

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

<div class="row mb-4">

<div class="col-md-8">

<h2 class="fw-bold">

<i class="fa-solid fa-folder-plus text-primary me-2"></i>

Add New Category

</h2>

<p class="text-muted">

Create a new category for organizing forum discussions.

</p>

</div>

<div class="col-md-4 text-end">

<a href="categories.php" class="btn btn-secondary">

<i class="fa-solid fa-arrow-left me-2"></i>

Back to Categories

</a>

</div>

</div>

<div class="row justify-content-center">

<div class="col-lg-8">

<div class="card shadow border-0">

<div class="card-header bg-primary text-white">

<h5 class="mb-0">

<i class="fa-solid fa-folder-plus me-2"></i>

Category Information

</h5>

</div>

<div class="card-body">

<form action="insert-category.php" method="POST">

<?php

if (isset($_SESSION['success'])) {

    echo '
        <div class="alert alert-success alert-dismissible fade show">

            <i class="fa-solid fa-circle-check me-2"></i>

            ' . $_SESSION['success'] . '

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert">
            </button>

        </div>
    ';

    unset($_SESSION['success']);

}

if (isset($_SESSION['error'])) {

    echo '
        <div class="alert alert-danger alert-dismissible fade show">

            <i class="fa-solid fa-circle-exclamation me-2"></i>

            ' . $_SESSION['error'] . '

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert">
            </button>

        </div>
    ';

    unset($_SESSION['error']);

}

?>

<div class="mb-4">

    <label class="form-label fw-bold">

        Category Name

        <span class="text-danger">*</span>

    </label>

    <input

        type="text"

        name="name"

        class="form-control"

        maxlength="100"

        placeholder="Enter category name"

        required>

    <small class="text-muted">

        Example: Web Development, Cybersecurity, Data Analysis

    </small>

</div>

<div class="mb-4">

    <label class="form-label fw-bold">

        Description

    </label>

    <textarea

        name="description"

        rows="6"

        class="form-control"

        placeholder="Enter a short description for this category..."></textarea>

    <small class="text-muted">

        This description will be displayed to users on the forum.

    </small>

</div>

<hr>

<div class="d-flex justify-content-between">

    <a

        href="categories.php"

        class="btn btn-secondary">

        <i class="fa-solid fa-arrow-left me-2"></i>

        Cancel

    </a>

    <button

        type="submit"

        class="btn btn-primary">

        <i class="fa-solid fa-floppy-disk me-2"></i>

        Save Category

    </button>

</div>

                    </form>

                </div>

            </div>

            <!-- Information Card -->

            <div class="card shadow-sm border-0 mt-4">

                <div class="card-header bg-light">

                    <h6 class="mb-0">

                        <i class="fa-solid fa-circle-info me-2 text-primary"></i>

                        Category Guidelines

                    </h6>

                </div>

                <div class="card-body">

                    <ul class="mb-0">

                        <li>
                            Choose a unique and meaningful category name.
                        </li>

                        <li>
                            Keep the description brief and easy to understand.
                        </li>

                        <li>
                            Avoid creating duplicate categories.
                        </li>

                        <li>
                            Categories help organize forum discussions effectively.
                        </li>

                    </ul>

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

    // Auto hide alerts

    const alerts = document.querySelectorAll(".alert");

    alerts.forEach(function(alert){

        setTimeout(function(){

            alert.classList.remove("show");

            alert.classList.add("fade");

            setTimeout(function(){

                alert.remove();

            },500);

        },5000);

    });

    // Auto capitalize category name

    const categoryName = document.querySelector('input[name="name"]');

    if(categoryName){

        categoryName.addEventListener("input", function(){

            this.value = this.value.replace(/\b\w/g,function(letter){

                return letter.toUpperCase();

            });

        });

    }

});

</script>

</body>

</html>