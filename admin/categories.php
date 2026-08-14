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
| SEARCH
|--------------------------------------------------------------------------
*/

$search = "";

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

$page = max($page, 1);

$offset = ($page - 1) * $limit;

/*
|--------------------------------------------------------------------------
| TOTAL RECORDS
|--------------------------------------------------------------------------
*/

$countQuery = "

SELECT COUNT(*)

FROM categories

WHERE name LIKE ?

";

$stmt = $pdo->prepare($countQuery);

$stmt->execute(["%{$search}%"]);

$totalCategories = $stmt->fetchColumn();

$totalPages = ceil($totalCategories / $limit);

/*
|--------------------------------------------------------------------------
| FETCH CATEGORIES
|--------------------------------------------------------------------------
*/

$query = "

SELECT

c.*,

COUNT(DISTINCT t.id) AS total_topics

FROM categories c

LEFT JOIN topics t

ON c.id = t.category_id

WHERE c.name LIKE ?

GROUP BY c.id

ORDER BY c.created_at DESC

LIMIT {$limit} OFFSET {$offset}

";

$stmt = $pdo->prepare($query);

$stmt->execute(["%{$search}%"]);

$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1.0">

<title>

Category Management

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

<i class="fa-solid fa-layer-group text-primary me-2"></i>

Category Management

</h2>

<p class="text-muted">

Create, edit and manage forum categories.

</p>

</div>

<div class="col-md-4 text-end">

<a
href="add-category.php"
class="btn btn-primary">

<i class="fa-solid fa-plus me-2"></i>

Add Category

</a>

</div>

</div>

<div class="card shadow border-0">

<div class="card-body">

<form method="GET">

<div class="row">

<div class="col-md-10">

<input

type="text"

name="search"

class="form-control"

placeholder="Search category..."

value="<?= htmlspecialchars($search); ?>">

</div>

<div class="col-md-2">

<button
class="btn btn-primary w-100">

<i class="fa-solid fa-magnifying-glass"></i>

Search

</button>

</div>

</div>

</form>

<hr>

<div class="table-responsive">

<table class="table table-hover align-middle">

    <thead class="table-dark">

        <tr>

            <th width="8%">#</th>

            <th>Category Name</th>

            <th>Description</th>

            <th class="text-center">Topics</th>

            <th class="text-center">Created</th>

            <th class="text-center">Actions</th>

        </tr>

    </thead>

    <tbody>

    <?php if(count($categories) > 0): ?>

        <?php foreach($categories as $index => $category): ?>

        <tr>

            <td>

                <?= $offset + $index + 1; ?>

            </td>

            <td>

                <strong>

                    <?= htmlspecialchars($category['name']); ?>

                </strong>

            </td>

            <td>

                <?php

                if(!empty($category['description'])){

                    echo nl2br(htmlspecialchars(substr($category['description'],0,80)));

                    if(strlen($category['description']) > 80){

                        echo "...";

                    }

                }else{

                    echo "<span class='text-muted'>No Description</span>";

                }

                ?>

            </td>

            <td class="text-center">

                <span class="badge bg-primary">

                    <?= $category['total_topics']; ?>

                </span>

            </td>

            <td class="text-center">

                <?= date("M d, Y", strtotime($category['created_at'])); ?>

            </td>

            <td class="text-center">

                <a

                href="../forum/topics.php?category=<?= $category['id']; ?>"

                class="btn btn-sm btn-success"

                title="View Topics">

                    <i class="fa-solid fa-eye"></i>

                </a>

                <a

                href="edit-category.php?id=<?= $category['id']; ?>"

                class="btn btn-sm btn-warning"

                title="Edit Category">

                    <i class="fa-solid fa-pen-to-square"></i>

                </a>

                <a

                href="delete-category.php?id=<?= $category['id']; ?>"

                class="btn btn-sm btn-danger"

                onclick="return confirm('Are you sure you want to delete this category?');"

                title="Delete Category">

                    <i class="fa-solid fa-trash"></i>

                </a>

            </td>

        </tr>

        <?php endforeach; ?>

    <?php else: ?>

        <tr>

            <td colspan="6" class="text-center py-5">

                <i class="fa-solid fa-folder-open fa-3x text-secondary mb-3"></i>

                <h5>No Categories Found</h5>

                <p class="text-muted">

                    There are no categories matching your search.

                </p>

            </td>

        </tr>

    <?php endif; ?>

    </tbody>

</table>

</div>

<hr>

<!-- Pagination -->

<?php if($totalPages > 1): ?>

<nav class="mt-4">

    <ul class="pagination justify-content-center">

        <!-- Previous -->

        <li class="page-item <?= ($page <= 1) ? 'disabled' : ''; ?>">

            <a
                class="page-link"
                href="?search=<?= urlencode($search); ?>&page=<?= $page-1; ?>">

                Previous

            </a>

        </li>

        <!-- Page Numbers -->

        <?php for($i = 1; $i <= $totalPages; $i++): ?>

        <li class="page-item <?= ($i == $page) ? 'active' : ''; ?>">

            <a
                class="page-link"
                href="?search=<?= urlencode($search); ?>&page=<?= $i; ?>">

                <?= $i; ?>

            </a>

        </li>

        <?php endfor; ?>

        <!-- Next -->

        <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : ''; ?>">

            <a
                class="page-link"
                href="?search=<?= urlencode($search); ?>&page=<?= $page+1; ?>">

                Next

            </a>

        </li>

    </ul>

</nav>

<?php endif; ?>

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

    // Auto-hide flash messages after 5 seconds
    const alerts = document.querySelectorAll(".alert");

    alerts.forEach(function(alert){

        setTimeout(function(){

            alert.classList.add("fade");

            setTimeout(function(){

                alert.remove();

            },500);

        },5000);

    });

});

</script>

</body>

</html>