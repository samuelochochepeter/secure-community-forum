<?php
require_once 'config/config.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Create Account | <?= SITE_NAME; ?></title>

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

<div class="col-lg-7 col-md-9">

<div class="card shadow-lg border-0 rounded-4">

<div class="card-body p-5">

<!-- Logo -->

<div class="text-center mb-4">

<i class="fa-solid fa-user-plus fa-3x text-primary mb-3"></i>

<h2 class="fw-bold">Create Account</h2>

<p class="text-muted">

Join the <?= SITE_NAME; ?> community today.

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

<!-- Registration Form -->

<form action="authentication/register-process.php" method="POST"
id="registerForm"
novalidate>

<div class="row">

<div class="col-md-6 mb-3">

<label for="fullname" class="form-label">

Full Name

</label>

<input
type="text"
id="fullname"
name="fullname"
class="form-control"
placeholder="Enter Full Name"
maxlength="100"
autocomplete="name"
required>

</div>

<div class="col-md-6 mb-3">

<label for="username" class="form-label">

Username

</label>

<input
type="text"
id="username"
name="username"
class="form-control"
placeholder="Choose Username"
maxlength="20"
autocomplete="username"
required>

</div>

</div>

<div class="mb-3">

<label for="email" class="form-label">

Email Address

</label>

<input
type="email"
id="email"
name="email"
class="form-control"
placeholder="example@gmail.com"
maxlength="100"
autocomplete="email"
required>

</div>

<div class="row">

<div class="col-md-6 mb-3">

<label for="password" class="form-label">

Password

</label>

<div class="input-group">

<input
type="password"
id="password"
name="password"
class="form-control"
autocomplete="new-password"
required>

<button
class="btn btn-outline-secondary"
type="button"
onclick="togglePassword('password', this)">

<i class="fa fa-eye"></i>

</button>

</div>

</div>

<div class="col-md-6 mb-3">

<label for="confirm_password" class="form-label">

Confirm Password

</label>

<div class="input-group">

<input
type="password"
id="confirm_password"
name="confirm_password"
class="form-control"
autocomplete="new-password"
required>

<button
class="btn btn-outline-secondary"
type="button"
onclick="togglePassword('confirm_password', this)">

<i class="fa fa-eye"></i>

</button>

</div>

</div>

</div>

<div class="mb-3">

<label for="bio" class="form-label">

Bio (Optional)

</label>

<textarea
id="bio"
name="bio"
class="form-control"
rows="3"
maxlength="300"
placeholder="Tell us something about yourself..."></textarea>

</div>

<!-- Password Strength -->

<div class="mb-3">

<div class="progress" style="height:8px;">

<div
id="passwordStrength"
class="progress-bar"
style="width:0%;">

</div>

</div>

<small
id="strengthText"
class="text-muted">

Password Strength

</small>

</div>

<!-- Terms -->

<div class="form-check mb-4">

<input
class="form-check-input"
type="checkbox"
name="terms"
id="terms"
required>

<label
class="form-check-label"
for="terms">

I agree to the Terms & Conditions

</label>

</div>

<div class="d-grid">

<button
type="submit"
class="btn btn-primary btn-lg">

<i class="fa-solid fa-user-plus me-2"></i>

Create Account

</button>

</div>

</form>

<hr>

<div class="text-center">

Already have an account?

<a
href="login.php"
class="text-decoration-none fw-semibold">

Login Here

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

// Show / Hide Password

function togglePassword(id, button){

const input = document.getElementById(id);

const icon = button.querySelector("i");

if(input.type === "password"){

input.type = "text";

icon.classList.replace("fa-eye","fa-eye-slash");

}else{

input.type = "password";

icon.classList.replace("fa-eye-slash","fa-eye");

}

}

// Password Strength

const password = document.getElementById("password");
const confirmPassword = document.getElementById("confirm_password");
const form = document.getElementById("registerForm");
const bar = document.getElementById("passwordStrength");
const text = document.getElementById("strengthText");

password.addEventListener("keyup", function(){

let score = 0;

const value = password.value;

if(value.length >= 8) score++;

if(/[A-Z]/.test(value)) score++;

if(/[a-z]/.test(value)) score++;

if(/[0-9]/.test(value)) score++;

if(/[^A-Za-z0-9]/.test(value)) score++;

bar.style.width = (score * 20) + "%";

if(score <= 2){

bar.className = "progress-bar bg-danger";

text.innerHTML = "Weak Password";

}
else if(score == 3){

bar.className = "progress-bar bg-warning";

text.innerHTML = "Medium Password";

}
else{

bar.className = "progress-bar bg-success";

text.innerHTML = "Strong Password";

}

});

// Confirm Password

form.addEventListener("submit", function(e){

if(password.value !== confirmPassword.value){

e.preventDefault();

alert("Passwords do not match.");

confirmPassword.focus();

}

});

</script>

</body>

</html>