<?php
require_once 'config/config.php';
require_once 'config/database.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Forum 2FA | Secure Online Community</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
          rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet"
          href="assets/css/style.css">

</head>

<body>

<!-- ===========================
NAVBAR
=========================== -->

<nav class="navbar navbar-expand-lg navbar-dark shadow-sm fixed-top">

<div class="container">

<a class="navbar-brand fw-bold"
href="index.php">

<i class="fa-solid fa-comments"></i>

Forum2FA

</a>

<button class="navbar-toggler"
type="button"
data-bs-toggle="collapse"
data-bs-target="#navbar">

<span class="navbar-toggler-icon"></span>

</button>

<div class="collapse navbar-collapse"
id="navbar">

<ul class="navbar-nav ms-auto">

<li class="nav-item">

<a class="nav-link active"
href="index.php">

Home

</a>

</li>

<li class="nav-item">

<a class="nav-link"
href="#categories">

Categories

</a>

</li>

<li class="nav-item">

<a class="nav-link"
href="#latest">

Discussions

</a>

</li>

<li class="nav-item">

<a class="nav-link"
href="about.php">

About

</a>

</li>

<li class="nav-item">

<a class="nav-link"
href="contact.php">

Contact

</a>

</li>

<li class="nav-item ms-lg-3">

<a href="login.php"
class="btn btn-outline-light">

Login

</a>

</li>

<li class="nav-item ms-lg-2">

<a href="register.php"
class="btn btn-warning">

Register

</a>

</li>

</ul>

</div>

</div>

</nav>

<!-- ===========================
HERO SECTION
=========================== -->

<section class="hero">

<div class="container">

<div class="row align-items-center">

<div class="col-lg-6">

<h1>

Join Nigeria's Secure Online Community

</h1>

<p>

Share ideas, ask questions, collaborate with professionals,
and enjoy secure discussions protected by Email Two-Factor Authentication.

</p>

<div class="mt-4">

<a href="register.php"
class="btn btn-warning btn-lg me-3">

<i class="fa-solid fa-user-plus"></i>

Create Account

</a>

<a href="login.php"
class="btn btn-outline-light btn-lg">

<i class="fa-solid fa-right-to-bracket"></i>

Login

</a>

</div>

</div>

<div class="col-lg-6">

<img src="assets/images/hero.jpg"
class="img-fluid hero-image"
alt="Forum">

</div>

</div>

</div>

</section>

<!-- ===========================
SEARCH
=========================== -->

<section class="search-section">

<div class="container">

<div class="row justify-content-center">

<div class="col-lg-8">

<form>

<div class="input-group input-group-lg shadow">

<input
type="text"
class="form-control"
placeholder="Search discussions, topics, users...">

<button class="btn btn-primary">

<i class="fa fa-search"></i>

Search

</button>

</div>

</form>

</div>

</div>

</div>

</section>

<!-- ===========================
STATISTICS
=========================== -->

<section class="stats py-5">

<div class="container">

<div class="row text-center">

<div class="col-md-3">

<div class="stat-card">

<i class="fa-solid fa-users"></i>

<h2>25K+</h2>

<p>Members</p>

</div>

</div>

<div class="col-md-3">

<div class="stat-card">

<i class="fa-solid fa-layer-group"></i>

<h2>8K+</h2>

<p>Topics</p>

</div>

</div>

<div class="col-md-3">

<div class="stat-card">

<i class="fa-solid fa-comments"></i>

<h2>75K+</h2>

<p>Replies</p>

</div>

</div>

<div class="col-md-3">

<div class="stat-card">

<i class="fa-solid fa-shield-halved"></i>

<h2>100%</h2>

<p>Secure Login</p>

</div>

</div>

</div>

</div>

</section>

<!-- ===========================
POPULAR CATEGORIES
=========================== -->

<section id="categories"
class="py-5">

<div class="container">

<div class="text-center mb-5">

<h2>

Popular Categories

</h2>

<p>

Explore discussions across different fields.

</p>

</div>

<div class="row">

<div class="col-lg-4 mb-4">

<div class="category-card">

<i class="fa-solid fa-code"></i>

<h4>Programming</h4>

<p>

HTML, CSS, JavaScript,
PHP,
Python,
Java,
C#

</p>

</div>

</div>

<div class="col-lg-4 mb-4">

<div class="category-card">

<i class="fa-solid fa-user-secret"></i>

<h4>Cybersecurity</h4>

<p>

Ethical Hacking,
Network Security,
Digital Forensics

</p>

</div>

</div>

<div class="col-lg-4 mb-4">

<div class="category-card">

<i class="fa-solid fa-robot"></i>

<h4>Artificial Intelligence</h4>

<p>

Machine Learning,
Deep Learning,
ChatGPT,
LLMs

</p>

</div>

</div>

<div class="col-lg-4 mb-4">

<div class="category-card">

<i class="fa-solid fa-network-wired"></i>

<h4>Networking</h4>

<p>

Cisco,
Packet Tracer,
Routing,
Switching

</p>

</div>

</div>

<div class="col-lg-4 mb-4">

<div class="category-card">

<i class="fa-solid fa-graduation-cap"></i>

<h4>Education</h4>

<p>

Assignments,
Projects,
Research,
Final Year

</p>

</div>

</div>

<div class="col-lg-4 mb-4">

<div class="category-card">

<i class="fa-solid fa-microchip"></i>

<h4>Technology</h4>

<p>

Innovation,
Cloud Computing,
IoT,
Software

</p>

</div>

</div>

</div>

</div>

</section>

<!-- ==========================================
LATEST DISCUSSIONS
========================================== -->

<section id="latest" class="latest-discussions py-5 bg-light">

    <div class="container">

        <div class="text-center mb-5">

            <h2 class="fw-bold">Latest Discussions</h2>

            <p class="text-muted">
                Join the latest conversations from our growing community.
            </p>

        </div>

        <div class="row">

            <div class="col-lg-4 mb-4">

                <div class="card discussion-card shadow-sm border-0 h-100">

                    <div class="card-body">

                        <span class="badge bg-primary mb-3">
                            Programming
                        </span>

                        <h5 class="card-title">
                            Best Practices for PHP Security
                        </h5>

                        <p class="card-text text-muted">

                            Learn how to secure your PHP applications
                            against SQL Injection, XSS and CSRF attacks.

                        </p>

                    </div>

                    <div class="card-footer bg-white border-0">

                        <small class="text-muted">

                            <i class="fa fa-user"></i>

                            Samuel Peter

                        </small>

                        <small class="float-end">

                            <i class="fa fa-comments"></i>

                            18 Replies

                        </small>

                    </div>

                </div>

            </div>

            <div class="col-lg-4 mb-4">

                <div class="card discussion-card shadow-sm border-0 h-100">

                    <div class="card-body">

                        <span class="badge bg-success mb-3">
                            Cybersecurity
                        </span>

                        <h5 class="card-title">

                            Building a Secure Authentication System

                        </h5>

                        <p class="card-text text-muted">

                            Password hashing, Sessions,
                            Email OTP and Secure Login.

                        </p>

                    </div>

                    <div class="card-footer bg-white border-0">

                        <small>

                            <i class="fa fa-user"></i>

                            Admin

                        </small>

                        <small class="float-end">

                            <i class="fa fa-comments"></i>

                            42 Replies

                        </small>

                    </div>

                </div>

            </div>

            <div class="col-lg-4 mb-4">

                <div class="card discussion-card shadow-sm border-0 h-100">

                    <div class="card-body">

                        <span class="badge bg-warning text-dark mb-3">

                            Artificial Intelligence

                        </span>

                        <h5 class="card-title">

                            AI Tools Every Developer Should Know

                        </h5>

                        <p class="card-text text-muted">

                            Discover powerful AI tools for
                            coding, debugging and productivity.

                        </p>

                    </div>

                    <div class="card-footer bg-white border-0">

                        <small>

                            <i class="fa fa-user"></i>

                            Moderator

                        </small>

                        <small class="float-end">

                            <i class="fa fa-comments"></i>

                            27 Replies

                        </small>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

<!-- ==========================================
WHY CHOOSE US
========================================== -->

<section class="why-us py-5">

    <div class="container">

        <div class="text-center mb-5">

            <h2 class="fw-bold">

                Why Choose Forum2FA?

            </h2>

        </div>

        <div class="row text-center">

            <div class="col-lg-3 mb-4">

                <div class="feature-box">

                    <i class="fa-solid fa-shield-halved fa-3x text-primary mb-3"></i>

                    <h5>Secure Login</h5>

                    <p>

                        Two-Factor Authentication protects
                        every user account.

                    </p>

                </div>

            </div>

            <div class="col-lg-3 mb-4">

                <div class="feature-box">

                    <i class="fa-solid fa-users fa-3x text-success mb-3"></i>

                    <h5>Community</h5>

                    <p>

                        Ask questions and receive answers
                        from experienced members.

                    </p>

                </div>

            </div>

            <div class="col-lg-3 mb-4">

                <div class="feature-box">

                    <i class="fa-solid fa-bolt fa-3x text-warning mb-3"></i>

                    <h5>Fast</h5>

                    <p>

                        Optimized for speed
                        and responsive on every device.

                    </p>

                </div>

            </div>

            <div class="col-lg-3 mb-4">

                <div class="feature-box">

                    <i class="fa-solid fa-lock fa-3x text-danger mb-3"></i>

                    <h5>Privacy</h5>

                    <p>

                        Modern security practices
                        keep your data protected.

                    </p>

                </div>

            </div>

        </div>

    </div>

</section>

<!-- ==========================================
CALL TO ACTION
========================================== -->

<section class="cta-section text-center">

    <div class="container">

        <h2 class="fw-bold">

            Ready to Join Our Community?

        </h2>

        <p class="lead">

            Register today and start engaging with thousands
            of technology enthusiasts.

        </p>

        <a href="register.php" class="btn btn-warning btn-lg me-3">

            Create Free Account

        </a>

        <a href="login.php" class="btn btn-outline-light btn-lg">

            Login

        </a>

    </div>

</section>

<!-- ==========================================
NEWSLETTER
========================================== -->

<section class="newsletter py-5 bg-light">

    <div class="container">

        <div class="row justify-content-center">

            <div class="col-lg-8 text-center">

                <h3 class="fw-bold">

                    Subscribe to Our Newsletter

                </h3>

                <p class="text-muted">

                    Stay informed with the latest discussions,
                    technology trends and community updates.

                </p>

                <form class="row g-2">

                    <div class="col-md-9">

                        <input
                            type="email"
                            class="form-control form-control-lg"
                            placeholder="Enter your email">

                    </div>

                    <div class="col-md-3">

                        <button
                            class="btn btn-primary btn-lg w-100">

                            Subscribe

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</section>

<!-- ==========================================
FOOTER
========================================== -->

<footer class="footer">

    <div class="container">

        <div class="row">

            <div class="col-lg-4">

                <h4>

                    <i class="fa-solid fa-comments"></i>

                    Forum2FA

                </h4>

                <p>

                    A secure online discussion platform with
                    Email Two-Factor Authentication.

                </p>

            </div>

            <div class="col-lg-2">

                <h5>Links</h5>

                <ul class="list-unstyled">

                    <li><a href="index.php">Home</a></li>

                    <li><a href="about.php">About</a></li>

                    <li><a href="contact.php">Contact</a></li>

                    <li><a href="register.php">Register</a></li>

                </ul>

            </div>

            <div class="col-lg-3">

                <h5>Resources</h5>

                <ul class="list-unstyled">

                    <li><a href="#">Help Center</a></li>

                    <li><a href="#">Privacy Policy</a></li>

                    <li><a href="#">Terms & Conditions</a></li>

                    <li><a href="#">Support</a></li>

                </ul>

            </div>

            <div class="col-lg-3">

                <h5>Follow Us</h5>

                <a href="#" class="me-3">

                    <i class="fab fa-facebook fa-2x"></i>

                </a>

                <a href="#" class="me-3">

                    <i class="fab fa-twitter fa-2x"></i>

                </a>

                <a href="#" class="me-3">

                    <i class="fab fa-linkedin fa-2x"></i>

                </a>

                <a href="#">

                    <i class="fab fa-github fa-2x"></i>

                </a>

            </div>

        </div>

        <hr>

        <div class="text-center">

            © <?php echo date("Y"); ?>

            Forum2FA. All Rights Reserved.

        </div>

    </div>

</footer>

<!-- Bootstrap -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JavaScript -->

<script src="assets/js/main.js"></script>

</body>

</html>