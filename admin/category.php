<?php

require_once "admin_auth.php";

require_permission("categories");

$result = $conn->query("
    SELECT 
        categories.id,
        categories.name,
        categories.icons,
        categories.icon,
        edu_lvls.name AS edu_name
    FROM categories
    INNER JOIN edu_lvls
        ON categories.edu_lvls_id = edu_lvls.id
    ORDER BY categories.id DESC
");

?>

<!DOCTYPE html>
<html>

<head>

<title>Categories</title>

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

<h1>Categories</h1>

</div>

<a href="category_add.php"
class="admin-button">

<i class="fa-solid fa-plus"></i>
Add Category

</a>

</div>


<div class="dashboard-panel">

<div class="admin-table-wrapper">

<table class="admin-table">

<thead>

<tr>

<th>ID</th>
<th>Education Level</th>
<th>Category</th>
<th>Icon</th>
<th>Actions</th>

</tr>

</thead>

<tbody>

<?php if ($result && $result->num_rows > 0): ?>

<?php while ($row = $result->fetch_assoc()): ?>

<tr>

<td><?= $row['id'] ?></td>

<td><?= htmlspecialchars($row['edu_name']) ?></td>

<td><?= htmlspecialchars($row['name']) ?></td>

<td>

<?php if ($row['icon']): ?>

<i class="<?= htmlspecialchars($row['icon']) ?>"></i>

<?php endif; ?>

</td>

<td>

<div class="table-actions">

<a href="category_edit.php?id=<?= $row['id'] ?>"
class="edit-button">

Edit

</a>

<a href="category_delete.php?id=<?= $row['id'] ?>"
class="delete-button"
onclick="return confirm('Delete this category?');">

Delete

</a>

</div>

</td>

</tr>

<?php endwhile; ?>

<?php else: ?>

<tr>

<td colspan="5"
class="empty-table">

No categories found.

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