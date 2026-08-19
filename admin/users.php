<?php

require_once "admin_auth.php";

if ($user['role'] !== 'owner') {

    header("Location: index.php");
    exit();

}

$result = $conn->query("
    SELECT id, name, email, role, created_at
    FROM users
    ORDER BY id DESC
");

?>

<!DOCTYPE html>
<html>

<head>

<title>User Management</title>

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
ACCOUNT MANAGEMENT
</p>

<h1>Users</h1>

</div>

</div>


<div class="dashboard-panel">

<div class="admin-table-wrapper">

<table class="admin-table">

<thead>

<tr>

<th>ID</th>
<th>Name</th>
<th>Email</th>
<th>Role</th>
<th>Created</th>
<th>Actions</th>

</tr>

</thead>

<tbody>

<?php while ($row = $result->fetch_assoc()): ?>

<tr>

<td><?= $row['id'] ?></td>

<td><?= htmlspecialchars($row['name']) ?></td>

<td><?= htmlspecialchars($row['email']) ?></td>

<td><?= htmlspecialchars($row['role']) ?></td>

<td><?= $row['created_at'] ?></td>

<td>

<div class="table-actions">

<a href="user_edit.php?id=<?= $row['id'] ?>"
class="edit-button">

Edit

</a>

<?php if ($row['role'] === 'staff'): ?>

<a href="staff_permissions.php?id=<?= $row['id'] ?>"
class="edit-button">

Permissions

</a>

<?php endif; ?>


<?php if ($row['role'] !== 'owner'): ?>

<a href="user_delete.php?id=<?= $row['id'] ?>"
class="delete-button"
onclick="return confirm('Delete this user?');">

Delete

</a>

<?php endif; ?>

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