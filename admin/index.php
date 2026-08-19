<?php

require_once "admin_auth.php";


/*
|--------------------------------------------------------------------------
| Dashboard counts
|--------------------------------------------------------------------------
*/

$edu_count = 0;
$category_count = 0;
$product_count = 0;
$stock_count = 0;
$order_count = 0;
$supplier_count = 0;


/*
|--------------------------------------------------------------------------
| Education levels
|--------------------------------------------------------------------------
*/

$edu_result = $conn->query("
    SELECT COUNT(*) AS total
    FROM edu_lvls
");

if ($edu_result) {
    $edu_count = $edu_result->fetch_assoc()['total'];
}


/*
|--------------------------------------------------------------------------
| Categories
|--------------------------------------------------------------------------
*/

$category_result = $conn->query("
    SELECT COUNT(*) AS total
    FROM categories
");

if ($category_result) {
    $category_count = $category_result->fetch_assoc()['total'];
}


/*
|--------------------------------------------------------------------------
| Products
|--------------------------------------------------------------------------
*/

$product_result = $conn->query("
    SELECT COUNT(*) AS total
    FROM products
");

if ($product_result) {
    $product_count = $product_result->fetch_assoc()['total'];
}


/*
|--------------------------------------------------------------------------
| Stock
|--------------------------------------------------------------------------
*/

$stock_result = $conn->query("
    SELECT COALESCE(SUM(stock), 0) AS total
    FROM products
");

if ($stock_result) {
    $stock_count = $stock_result->fetch_assoc()['total'];
}


/*
|--------------------------------------------------------------------------
| Orders
|--------------------------------------------------------------------------
*/

$order_result = $conn->query("
    SELECT COUNT(*) AS total
    FROM orders
");

if ($order_result) {
    $order_count = $order_result->fetch_assoc()['total'];
}


/*
|--------------------------------------------------------------------------
| Suppliers
|--------------------------------------------------------------------------
*/

$supplier_result = $conn->query("
    SELECT COUNT(*) AS total
    FROM suppliers
");

if ($supplier_result) {
    $supplier_count = $supplier_result->fetch_assoc()['total'];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Admin Dashboard</title>

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <link rel="stylesheet"
          href="adm.css">

</head>

<body>

<div class="admin-layout">

    <?php include "sidebar.php"; ?>


    <main class="admin-main">

        <div class="admin-topbar">

            <div>

                <p class="dashboard-label">
                    STORE MANAGEMENT
                </p>

                <h1>Dashboard</h1>

            </div>

            <div class="admin-user">

                <i class="fa-solid fa-circle-user"></i>

                <div>
                    <strong>
                        <?= htmlspecialchars($user['name']) ?>
                    </strong>

                    <span>
                        <?= htmlspecialchars(ucfirst($user['role'])) ?>
                    </span>
                </div>

            </div>

        </div>


        <div class="welcome-panel">

            <div>

                <p class="dashboard-label">
                    WELCOME BACK
                </p>

                <h2>
                    Hello, <?= htmlspecialchars($user['name']) ?>
                </h2>

                <p>
                    Manage your stationery store from the admin dashboard.
                </p>

            </div>

        </div>


        <div class="dashboard-grid">


            <?php if ($user['role'] === 'owner'): ?>

            <div class="dashboard-card">

                <div class="dashboard-card-icon">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>

                <div>
                    <span>Education Levels</span>
                    <strong><?= $edu_count ?></strong>
                </div>

            </div>

            <?php endif; ?>


            <?php if (has_permission("categories")): ?>

            <div class="dashboard-card">

                <div class="dashboard-card-icon">
                    <i class="fa-solid fa-layer-group"></i>
                </div>

                <div>
                    <span>Categories</span>
                    <strong><?= $category_count ?></strong>
                </div>

            </div>

            <?php endif; ?>


            <?php if (has_permission("products")): ?>

            <div class="dashboard-card">

                <div class="dashboard-card-icon">
                    <i class="fa-solid fa-box"></i>
                </div>

                <div>
                    <span>Products</span>
                    <strong><?= $product_count ?></strong>
                </div>

            </div>

            <?php endif; ?>


            <?php if (has_permission("inventory")): ?>

            <div class="dashboard-card">

                <div class="dashboard-card-icon">
                    <i class="fa-solid fa-warehouse"></i>
                </div>

                <div>
                    <span>Total Stock</span>
                    <strong><?= $stock_count ?></strong>
                </div>

            </div>

            <?php endif; ?>


            <?php if (has_permission("orders")): ?>

            <div class="dashboard-card">

                <div class="dashboard-card-icon">
                    <i class="fa-solid fa-cart-shopping"></i>
                </div>

                <div>
                    <span>Orders</span>
                    <strong><?= $order_count ?></strong>
                </div>

            </div>

            <?php endif; ?>


            <?php if (has_permission("suppliers")): ?>

            <div class="dashboard-card">

                <div class="dashboard-card-icon">
                    <i class="fa-solid fa-truck-field"></i>
                </div>

                <div>
                    <span>Suppliers</span>
                    <strong><?= $supplier_count ?></strong>
                </div>

            </div>

            <?php endif; ?>


        </div>


        <div class="dashboard-panel">

            <div class="panel-heading">

                <div>
                    <p class="dashboard-label">
                        QUICK ACCESS
                    </p>

                    <h2>Store Management</h2>
                </div>

            </div>


            <div class="quick-actions">


                <?php if ($user['role'] === 'owner'): ?>

                <a href="edu_lvls.php"
                   class="quick-action">

                    <i class="fa-solid fa-graduation-cap"></i>

                    <span>
                        Education Levels
                    </span>

                </a>

                <?php endif; ?>


                <?php if (has_permission("categories")): ?>

                <a href="categories.php"
                   class="quick-action">

                    <i class="fa-solid fa-layer-group"></i>

                    <span>
                        Categories
                    </span>

                </a>

                <?php endif; ?>


                <?php if (has_permission("products")): ?>

                <a href="products.php"
                   class="quick-action">

                    <i class="fa-solid fa-box"></i>

                    <span>
                        Products
                    </span>

                </a>

                <?php endif; ?>


                <?php if (has_permission("inventory")): ?>

                <a href="inventory.php"
                   class="quick-action">

                    <i class="fa-solid fa-warehouse"></i>

                    <span>
                        Inventory
                    </span>

                </a>

                <?php endif; ?>


                <?php if (has_permission("stock_in")): ?>

                <a href="stock_in.php"
                   class="quick-action">

                    <i class="fa-solid fa-arrow-right-to-bracket"></i>

                    <span>
                        Stock In
                    </span>

                </a>

                <?php endif; ?>


                <?php if (has_permission("stock_out")): ?>

                <a href="stock_out.php"
                   class="quick-action">

                    <i class="fa-solid fa-arrow-right-from-bracket"></i>

                    <span>
                        Stock Out
                    </span>

                </a>

                <?php endif; ?>


                <?php if (has_permission("suppliers")): ?>

                <a href="suppliers.php"
                   class="quick-action">

                    <i class="fa-solid fa-truck-field"></i>

                    <span>
                        Suppliers
                    </span>

                </a>

                <?php endif; ?>


                <?php if (has_permission("orders")): ?>

                <a href="order_items.php"
                   class="quick-action">

                    <i class="fa-solid fa-cart-shopping"></i>

                    <span>
                        Order Items
                    </span>

                </a>

                <?php endif; ?>


            </div>

        </div>

    </main>

</div>

</body>
</html>