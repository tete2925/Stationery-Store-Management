<?php

require_once "admin_auth.php";

require_permission("inventory");

$result = $conn->query("
    SELECT
        inventory.id,
        inventory.product_id,
        inventory.type,
        inventory.quantity,
        inventory.note,
        inventory.created_at,
        products.name AS product_name
    FROM inventory
    INNER JOIN products
        ON inventory.product_id = products.id
    ORDER BY inventory.id DESC
");

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<title>Inventory</title>

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

<p class="dashboard-label">INVENTORY</p>

<h1>Inventory</h1>

</div>

<a href="inventory_add.php" class="admin-button">

<i class="fa-solid fa-plus"></i>

Add Movement

</a>

</div>


<div class="dashboard-panel">

<div class="admin-table-wrapper">

<table class="admin-table">

<thead>

<tr>

<th>ID</th>
<th>Product</th>
<th>Type</th>
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

<td><?= $row['id'] ?></td>

<td>
<?= htmlspecialchars($row['product_name']) ?>
</td>

<td>

<?php if ($row['type'] === 'IN'): ?>

<span class="status-badge status-in">
IN
</span>

<?php else: ?>

<span class="status-badge status-out">
OUT
</span>

<?php endif; ?>

</td>

<td><?= $row['quantity'] ?></td>

<td><?= htmlspecialchars($row['note']) ?></td>

<td><?= htmlspecialchars($row['created_at']) ?></td>

<td class="action-buttons">

<a href="inventory_edit.php?id=<?= $row['id'] ?>"
   class="edit-button">

Edit

</a>

<a href="inventory_delete.php?id=<?= $row['id'] ?>"
   class="delete-button"
   onclick="return confirm('Delete this inventory movement?');">

Delete

</a>

</td>

</tr>

<?php endwhile; ?>

<?php else: ?>

<tr>

<td colspan="7" class="empty-message">

No inventory movements found.

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