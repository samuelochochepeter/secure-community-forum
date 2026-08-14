<?php
require_once 'config/config.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login | <?= SITE_NAME; ?></title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body class="bg-light">

<div class="container py-5">

<div class="row justify-content-center">

<div class="col-lg-6 col-md-8">

<div class="card shadow-lg border-0 rounded-4">

<div class="card-body p-5">

<!-- Logo -->

<div class="text-center mb-4">

<i class="fa-solid fa-right-to-bracket fa-3x text-primary mb-3"></i>

<h2 class="fw-bold">

Welcome Back

</h2>

<p class="text-muted">

Sign in to your <?= SITE_NAME; ?> account.

</p>

</div>

<!-- Success Message -->

<?php if(isset($_SESSION['success'])): ?>

<div class="alert alert-success alert-dismissible fade show" role="alert">

<?= htmlspecialchars($_SESSION['success']); ?>

<button
type="button"
class="btn-close"
data-bs-dismiss="alert">
</button>

</div>

<?php unset($_SESSION['success']); endif; ?>

<!-- Error Message -->

<?php if(isset($_SESSION['error'])): ?>

<div class="alert alert-danger alert-dismissible fade show" role="alert">

<?= htmlspecialchars($_SESSION['error']); ?>

<button
type="button"
class="btn-close"
data-bs-dismiss="alert">
</button>

</div>

<?php unset($_SESSION['error']); endif; ?>

<!-- Login Form -->

<form
action="authentication/login-process.php"
method="POST"
id="loginForm"
novalidate>

<div class="mb-3">

<label for="email" class="form-label">

Email Address

</label>

<input
type="email"
id="email"
name="email"
class="form-control"
placeholder="Enter your email address"
autocomplete="email"
required>

</div>

<div class="mb-3">

<label for="password" class="form-label">

Password

</label>

<div class="input-group">

<input
type="password"
id="password"
name="password"
class="form-control"
placeholder="Enter your password"
autocomplete="current-password"
required>

<button
class="btn btn-outline-secondary"
type="button"
onclick="togglePassword()">

<i class="fa fa-eye"></i>

</button>

</div>

</div>

<div class="d-flex justify-content-between align-items-center mb-4">

<div class="form-check">

<input
class="form-check-input"
type="checkbox"
name="remember_me"
id="remember_me">

<label
class="form-check-label"
for="remember_me">

Remember Me

</label>

</div>

<a
href="authentication/forgot-password.php"
class="text-decoration-none">

Forgot Password?

</a>

</div>

<div class="d-grid">

<button
type="submit"
class="btn btn-primary btn-lg">

<i class="fa-solid fa-right-to-bracket me-2"></i>

Login

</button>

</div>

</form>

<hr>

<div class="text-center">

Don't have an account?

<a
href="register.php"
class="text-decoration-none fw-semibold">

Create Account

</a>

</div>

</div>

</div>

</div>

</div>

</div>

<!-- Bootstrap -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>

function togglePassword(){

const input = document.getElementById("password");

const icon = document.querySelector(".input-group button i");

if(input.type === "password"){

input.type = "text";

icon.classList.replace("fa-eye","fa-eye-slash");

}else{

input.type = "password";

icon.classList.replace("fa-eye-slash","fa-eye");

}

}

document.getElementById("loginForm").addEventListener("submit", function(e){

const email = document.getElementById("email").value.trim();

const password = document.getElementById("password").value.trim();

if(email === "" || password === ""){

e.preventDefault();

alert("Please enter your email address and password.");

}

});

</script>

</body>

</html>