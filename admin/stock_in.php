<?php

require_once "admin_auth.php";

require_permission("stock_in");

$result = $conn->query("
    SELECT
        inventory.id,
        inventory.product_id,
        inventory.quantity,
        inventory.note,
        inventory.created_at,
        products.name AS product_name
    FROM inventory
    INNER JOIN products
        ON inventory.product_id = products.id
    WHERE inventory.type = 'IN'
    ORDER BY inventory.id DESC
");

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<title>Stock In</title>

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

<h1>Stock In</h1>

</div>


<a href="stock_in_add.php"
   class="admin-button">

<i class="fa-solid fa-plus"></i>

Stock In

</a>

</div>


<div class="dashboard-panel">

<div class="admin-table-wrapper">

<table class="admin-table">

<thead>

<tr>

<th>ID</th>

<th>Product</th>

<th>Quantity</th>

<th>Note</th>

<th>Date</th>

<th>Actions</th>

</tr>

</thead>


<tbody>

<?php if ($result && $result->num_rows > 0): ?>

<?php while ($row = $result->fetch_assoc()): ?>

<tr>

<td>
<?= $row['id'] ?>
</td>


<td>
<?= htmlspecialchars($row['product_name']) ?>
</td>


<td>
<?= $row['quantity'] ?>
</td>


<td>
<?= htmlspecialchars($row['note']) ?>
</td>


<td>
<?= htmlspecialchars($row['created_at']) ?>
</td>


<td class="action-buttons">

<a href="stock_in_delete.php?id=<?= $row['id'] ?>"
   class="delete-button"
   onclick="return confirm('Delete this stock-in record?');">

Delete

</a>

</td>

</tr>

<?php endwhile; ?>

<?php else: ?>

<tr>

<td colspan="6"
    class="empty-message">

No stock-in records found.

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