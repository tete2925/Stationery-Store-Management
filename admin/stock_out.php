<?php

require_once "admin_auth.php";

require_permission("stock_out");

/*
|--------------------------------------------------------------------------
| Get total sold quantity for each product
|--------------------------------------------------------------------------
| Every order_item represents a customer purchase.
| We GROUP BY product_id so the same product appears only once.
*/

$result = $conn->query("
    SELECT
        p.id AS product_id,
        p.name AS product_name,
        COALESCE(SUM(oi.quantity), 0) AS quantity
    FROM products p
    LEFT JOIN order_items oi
        ON p.id = oi.product_id
    GROUP BY p.id, p.name
    HAVING quantity > 0
    ORDER BY p.name ASC
");

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<title>Stock Out</title>

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
INVENTORY
</p>

<h1>
Stock Out
</h1>

</div>

</div>


<div class="dashboard-panel">

<div class="admin-table-wrapper">

<table class="admin-table">

<thead>

<tr>

<th>ID</th>

<th>Product</th>

<th>Total Sold</th>

</tr>

</thead>


<tbody>

<?php if ($result && $result->num_rows > 0): ?>

<?php while ($row = $result->fetch_assoc()): ?>

<tr>

<td>
<?= (int)$row['product_id'] ?>
</td>

<td>
<?= htmlspecialchars($row['product_name']) ?>
</td>

<td>
<?= (int)$row['quantity'] ?>
</td>

</tr>

<?php endwhile; ?>

<?php else: ?>

<tr>

<td colspan="3"
    class="empty-message">

No products have been sold yet.

</td>

</tr>

<?php endif; ?>

</tbody>

</table>

</div>

</div>

</main>

</div>

</body>

</html>