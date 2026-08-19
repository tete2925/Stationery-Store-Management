<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once __DIR__ . "/db.php";

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Student Stationery</title>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    >

    <link
        rel="stylesheet"
        href="/stationary/css/style.css"
    >

</head>


<body>


<header class="site-header">


    <!-- =====================================================
         TOP BAR
    ====================================================== -->

    <div class="top-bar">

        <div class="container top-content">

            <span>
                09 777 888 999
            </span>

            <span>
                Maubin
            </span>

        </div>

    </div>


    <!-- =====================================================
         MAIN HEADER
    ====================================================== -->

    <div class="main-header">

        <div class="container header-content">


            <!-- LOGO -->

            <a
                href="/stationary/index.php"
                class="logo"
            >

                <span class="logo-icon">

                    <i class="fa-solid fa-pencil"></i>

                </span>

                <span class="logo-text">

                    Student
                    <strong>Stationery</strong>

                </span>

            </a>


            <!-- SEARCH -->

            <form
                class="search-box"
                action="/stationary/products.php"
                method="GET"
            >

                <input
                    type="text"
                    name="search"
                    placeholder="Search for products..."
                >

                <button type="submit">

                    <i class="fa-solid fa-magnifying-glass"></i>

                </button>

            </form>


            <!-- ACTIONS -->

            <div class="header-actions">


                <!-- ACCOUNT -->

                <a
                    href="/stationary/auth/login.php"
                    aria-label="Account"
                >

                    <i class="fa-solid fa-user"></i>

                </a>


                <!-- CART -->

                <a
                    href="/stationary/cart.php"
                    class="cart-link"
                    aria-label="Cart"
                >

                    <i class="fa-solid fa-cart-shopping"></i>

                    <span id="cart-count">
                        0
                    </span>

                </a>


            </div>

        </div>

    </div>


    <!-- =====================================================
         NAVIGATION
    ====================================================== -->

    <nav class="navigation">

        <div class="container nav-content">


            <a href="/stationary/index.php">
                Home
            </a>


            <a href="/stationary/primary.php">
                Primary School
            </a>


            <a href="/stationary/highsch.php">
                High School
            </a>


            <a href="/stationary/uni.php">
                University
            </a>


            <a href="/stationary/products.php">
                All Products
            </a>


        </div>

    </nav>


</header>