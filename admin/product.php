<?php

require_once "admin_auth.php";

require_permission("products");

$result = $conn->query("
    SELECT 
        products.*,
        categories.name AS category_name,
        edu_lvls.name AS edu_name
    FROM products

    INNER JOIN categories
        ON products.category_id = categories.id

    INNER JOIN edu_lvls
        ON categories.edu_lvls_id = edu_lvls.id

    ORDER BY products.id DESC
");

?>

<!DOCTYPE html>
<html>

<head>

<title>Products</title>

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

<h1>Products</h1>

</div>

<a href="product_add.php"
class="admin-button">

<i class="fa-solid fa-plus"></i>
Add Product

</a>

</div>


<div class="dashboard-panel">

<div class="admin-table-wrapper">

<table class="admin-table">

<thead>

<tr>

<th>ID</th>
<th>Image</th>
<th>Product</th>
<th>Education</th>
<th>Category</th>
<th>Price</th>
<th>Stock</th>
<th>Actions</th>

</tr>

</thead>

<tbody>

<?php if ($result && $result->num_rows > 0): ?>

<?php while ($row = $result->fetch_assoc()): ?>

<tr>

<td><?= $row['id'] ?></td>

<td>

<?php if (!empty($row['image'])): ?>

<img src="../images/<?= htmlspecialchars($row['image']) ?>"
style="width:50px;height:50px;object-fit:cover;border-radius:5px;">

<?php endif; ?>

</td>

<td><?= htmlspecialchars($row['name']) ?></td>

<td><?= htmlspecialchars($row['edu_name']) ?></td>

<td><?= htmlspecialchars($row['category_name']) ?></td>

<td><?= number_format($row['price'], 2) ?> MMK</td>

<td><?= $row['stock'] ?></td>

<td>

<div class="table-actions">

<a href="product_edit.php?id=<?= $row['id'] ?>"
class="edit-button">

Edit

</a>

<a href="product_delete.php?id=<?= $row['id'] ?>"
class="delete-button"
onclick="return confirm('Delete this product?');">

Delete

</a>

</div>

</td>

</tr>

<?php endwhile; ?>

<?php else: ?>

<tr>

<td colspan="8"
class="empty-table">

No products found.

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