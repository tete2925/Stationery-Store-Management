<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "admin_auth.php";

?>

<aside class="admin-sidebar">

    <!-- LOGO -->

    <div class="admin-logo">

        <div class="admin-logo-icon">
            <i class="fa-solid fa-pencil"></i>
        </div>

        <div class="admin-logo-text">
            Student
            <strong>Stationery</strong>
        </div>

    </div>


    <!-- NAVIGATION -->

    <nav class="admin-nav">


        <!-- DASHBOARD -->

        <a href="index.php"
           class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>">

            <i class="fa-solid fa-gauge-high"></i>

            <span>
                Dashboard
            </span>

        </a>


        <!-- STORE MANAGEMENT -->

        <p class="nav-title">
            STORE MANAGEMENT
        </p>


        <!-- Primary School -->

        <a href="../primary.php"
           class="nav-link">

            <i class="fa-solid fa-graduation-cap"></i>

            <span>
                Primary School
            </span>

        </a>


        <!-- High School -->

        <a href="../highsch.php"
           class="nav-link">

            <i class="fa-solid fa-school"></i>

            <span>
                High School
            </span>

        </a>


        <!-- University -->

        <a href="../uni.php"
           class="nav-link">

            <i class="fa-solid fa-building-columns"></i>

            <span>
                University
            </span>

        </a>


        <!-- Categories -->

        <?php if (has_permission("categories")): ?>

            <a href="category.php"
               class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'category.php' ? 'active' : '' ?>">

                <i class="fa-solid fa-layer-group"></i>

                <span>
                    Categories
                </span>

            </a>

        <?php endif; ?>


        <!-- Products -->

        <?php if (has_permission("products")): ?>

            <a href="product.php"
               class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'product.php' ? 'active' : '' ?>">

                <i class="fa-solid fa-box"></i>

                <span>
                    Products
                </span>

            </a>

        <?php endif; ?>


        <!-- Inventory -->


        <!-- Stock In -->

        <?php if (has_permission("stock_in")): ?>

            <a href="stock_in.php"
               class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'stock_in.php' ? 'active' : '' ?>">

                <i class="fa-solid fa-arrow-right-to-bracket"></i>

                <span>
                    Stock In
                </span>

            </a>

        <?php endif; ?>


        <!-- Stock Out -->

        <?php if (has_permission("stock_out")): ?>

            <a href="stock_out.php"
               class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'stock_out.php' ? 'active' : '' ?>">

                <i class="fa-solid fa-arrow-right-from-bracket"></i>

                <span>
                    Stock Out
                </span>

            </a>

        <?php endif; ?>


        <!-- Suppliers -->

        <?php if (has_permission("suppliers")): ?>

            <a href="suppliers.php"
               class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'suppliers.php' ? 'active' : '' ?>">

                <i class="fa-solid fa-truck-field"></i>

                <span>
                    Suppliers
                </span>

            </a>

        <?php endif; ?>


        <!-- Orders -->

        <?php if (has_permission("orders")): ?>

            <a href="order.php"
               class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'order.php' ? 'active' : '' ?>">

                <i class="fa-solid fa-receipt"></i>

                <span>
                    Orders
                </span>

            </a>


            <!-- Order Items -->

            <a href="order_items.php"
               class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'order_items.php' ? 'active' : '' ?>">

                <i class="fa-solid fa-cart-shopping"></i>

                <span>
                    Order Items
                </span>

            </a>


            <!-- Delivery Info -->

            <a href="delinfo.php"
               class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'delinfo.php' ? 'active' : '' ?>">

                <i class="fa-solid fa-truck"></i>

                <span>
                    Delivery Info
                </span>

            </a>

        <?php endif; ?>


        <!-- ACCOUNT MANAGEMENT -->

        <?php if (($_SESSION['user_role'] ?? '') === 'owner'): ?>

            <p class="nav-title">
                ACCOUNT MANAGEMENT
            </p>


            <a href="users.php"
               class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'users.php' ? 'active' : '' ?>">

                <i class="fa-solid fa-users"></i>

                <span>
                    Users
                </span>

            </a>

        <?php endif; ?>


    </nav>


    <!-- SIDEBAR BOTTOM -->

    <div class="sidebar-bottom">

        <a href="../auth/logout.php"
           class="nav-link logout-link">

            <i class="fa-solid fa-right-from-bracket"></i>

            <span>
                Logout
            </span>

        </a>

    </div>


</aside>



<script>
document.addEventListener("DOMContentLoaded", function () {

    const sidebar = document.querySelector(".admin-nav");

    if (!sidebar) return;

    const savedScroll = sessionStorage.getItem("adminSidebarScroll");

    if (savedScroll !== null) {
        sidebar.scrollTop = parseInt(savedScroll, 10);
    }

    document.querySelectorAll(".admin-nav .nav-link").forEach(function (link) {

        link.addEventListener("click", function () {

            sessionStorage.setItem(
                "adminSidebarScroll",
                sidebar.scrollTop
            );

        });

    });

});
</script>