/*
==========================================================
FORUM 2FA
Main JavaScript File
Version: 1.0
==========================================================
*/

"use strict";

/* ==========================================================
   DOM READY
========================================================== */

document.addEventListener("DOMContentLoaded", function () {

    initStickyNavbar();

    initSmoothScroll();

    initScrollTop();

    initActiveNavigation();

    initNewsletter();

    initSearch();

    initRevealAnimation();

    hideLoader();

});


/* ==========================================================
   STICKY NAVBAR
========================================================== */

function initStickyNavbar() {

    const navbar = document.querySelector(".navbar");

    if (!navbar) return;

    window.addEventListener("scroll", function () {

        if (window.scrollY > 80) {

            navbar.style.padding = "10px 0";

            navbar.style.boxShadow = "0 10px 25px rgba(0,0,0,.15)";

        } else {

            navbar.style.padding = "16px 0";

            navbar.style.boxShadow = "none";

        }

    });

}


/* ==========================================================
   SMOOTH SCROLL
========================================================== */

function initSmoothScroll() {

    document.querySelectorAll('a[href^="#"]').forEach(anchor => {

        anchor.addEventListener("click", function (e) {

            const target = document.querySelector(this.getAttribute("href"));

            if (target) {

                e.preventDefault();

                target.scrollIntoView({

                    behavior: "smooth",

                    block: "start"

                });

            }

        });

    });

}


/* ==========================================================
   SCROLL TO TOP
========================================================== */

function initScrollTop() {

    const button = document.createElement("div");

    button.className = "scroll-top";

    button.innerHTML = '<i class="fa-solid fa-arrow-up"></i>';

    document.body.appendChild(button);

    window.addEventListener("scroll", function () {

        if (window.scrollY > 300) {

            button.classList.add("active");

        } else {

            button.classList.remove("active");

        }

    });

    button.addEventListener("click", function () {

        window.scrollTo({

            top: 0,

            behavior: "smooth"

        });

    });

}


/* ==========================================================
   ACTIVE NAVIGATION
========================================================== */

function initActiveNavigation() {

    const sections = document.querySelectorAll("section");

    const navLinks = document.querySelectorAll(".navbar .nav-link");

    window.addEventListener("scroll", function () {

        let current = "";

        sections.forEach(section => {

            const sectionTop = section.offsetTop - 120;

            if (window.pageYOffset >= sectionTop) {

                current = section.getAttribute("id");

            }

        });

        navLinks.forEach(link => {

            link.classList.remove("active");

            if (link.getAttribute("href") === "#" + current) {

                link.classList.add("active");

            }

        });

    });

}


/* ==========================================================
   NEWSLETTER VALIDATION
========================================================== */

function initNewsletter() {

    const form = document.querySelector(".newsletter form");

    if (!form) return;

    form.addEventListener("submit", function (e) {

        e.preventDefault();

        const email = form.querySelector("input").value.trim();

        if (email === "") {

            alert("Please enter your email address.");

            return;

        }

        const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!pattern.test(email)) {

            alert("Please enter a valid email address.");

            return;

        }

        alert("Thank you for subscribing!");

        form.reset();

    });

}


/* ==========================================================
   SEARCH BAR
========================================================== */

function initSearch() {

    const input = document.querySelector(".search-section input");

    const button = document.querySelector(".search-section button");

    if (!input || !button) return;

    function search() {

        const keyword = input.value.trim();

        if (keyword === "") {

            alert("Please enter a search keyword.");

            input.focus();

            return;

        }

        alert("Searching for: " + keyword);

    }

    button.addEventListener("click", function (e) {

        e.preventDefault();

        search();

    });

    input.addEventListener("keypress", function (e) {

        if (e.key === "Enter") {

            e.preventDefault();

            search();

        }

    });

}


/* ==========================================================
   REVEAL ANIMATION
========================================================== */

function initRevealAnimation() {

    const elements = document.querySelectorAll(

        ".category-card,.discussion-card,.feature-box,.stat-card"

    );

    const observer = new IntersectionObserver(

        entries => {

            entries.forEach(entry => {

                if (entry.isIntersecting) {

                    entry.target.style.opacity = "1";

                    entry.target.style.transform = "translateY(0)";

                }

            });

        },

        {

            threshold: 0.2

        }

    );

    elements.forEach(element => {

        element.style.opacity = "0";

        element.style.transform = "translateY(40px)";

        element.style.transition = "all .7s ease";

        observer.observe(element);

    });

}


/* ==========================================================
   PAGE LOADER
========================================================== */

function hideLoader() {

    const loader = document.querySelector(".loader");

    if (!loader) return;

    window.addEventListener("load", function () {

        loader.style.opacity = "0";

        loader.style.visibility = "hidden";

        setTimeout(() => {

            loader.remove();

        }, 500);

    });

}


/* ==========================================================
   FUTURE DARK MODE SUPPORT
========================================================== */

function toggleDarkMode() {

    document.body.classList.toggle("dark-mode");

}


/* ==========================================================
   HELPER: SHOW ALERT
========================================================== */

function showMessage(message, type = "info") {

    console.log(type.toUpperCase() + ": " + message);

}