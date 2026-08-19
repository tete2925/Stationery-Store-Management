<?php

require_once "admin_auth.php";

require_permission("suppliers");

$result = $conn->query("
    SELECT *
    FROM suppliers
    ORDER BY id DESC
");

?>

<!DOCTYPE html>
<html>

<head>

<title>Suppliers</title>

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
BUSINESS
</p>

<h1>Suppliers</h1>

</div>

<a href="supplier_add.php"
class="admin-button">

<i class="fa-solid fa-plus"></i>
Add Supplier

</a>

</div>


<div class="dashboard-panel">

<div class="admin-table-wrapper">

<table class="admin-table">

<thead>

<tr>

<th>ID</th>
<th>Name</th>
<th>Phone</th>
<th>Email</th>
<th>Address</th>
<th>Actions</th>

</tr>

</thead>

<tbody>

<?php while ($row = $result->fetch_assoc()): ?>

<tr>

<td><?= $row['id'] ?></td>

<td><?= htmlspecialchars($row['name']) ?></td>

<td><?= htmlspecialchars($row['phone'] ?? '') ?></td>

<td><?= htmlspecialchars($row['email'] ?? '') ?></td>

<td><?= htmlspecialchars($row['address'] ?? '') ?></td>

<td>

<div class="table-actions">

<a href="supplier_edit.php?id=<?= $row['id'] ?>"
class="edit-button">

Edit

</a>

<a href="supplier_delete.php?id=<?= $row['id'] ?>"
class="delete-button"
onclick="return confirm('Delete this supplier?');">

Delete

</a>

</div>

</td>

</tr>

<?php endwhile; ?>

</tbody>

</table>

</div>

</div>

</main>

</div>

</body>

</html>