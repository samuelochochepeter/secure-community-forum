<nav class="navbar navbar-expand-lg bg-white shadow-sm px-4">

<div class="container-fluid">

<form class="d-flex w-50">

<input
class="form-control"
type="search"
placeholder="Search discussions...">

</form>

<ul class="navbar-nav ms-auto align-items-center">

<li class="nav-item me-3">

<a class="nav-link position-relative" href="#">

<i class="fa-solid fa-bell fa-lg"></i>

<span
class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">

3

</span>

</a>

</li>

<li class="nav-item dropdown">

<a
class="nav-link dropdown-toggle"
href="#"
role="button"
data-bs-toggle="dropdown">

<img
src="../uploads/default-avatar.png"
width="40"
height="40"
class="rounded-circle me-2">

<?= $_SESSION['fullname']; ?>

</a>

<ul class="dropdown-menu dropdown-menu-end">

<li>

<a class="dropdown-item" href="../profile/index.php">

Profile

</a>

</li>

<li>

<a class="dropdown-item" href="../settings/index.php">

Settings

</a>

</li>

<li><hr class="dropdown-divider"></li>

<li>

<a class="dropdown-item text-danger"

href="../authentication/logout.php">

Logout

</a>

</li>

</ul>

</li>

</ul>

</div>

</nav>