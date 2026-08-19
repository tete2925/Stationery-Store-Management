<?php

require_once "admin_auth.php";

if ($user['role'] !== 'owner') {

    header("Location: index.php");
    exit();

}

$id = intval($_GET['id'] ?? 0);

$stmt = $conn->prepare("
    SELECT *
    FROM users
    WHERE id = ?
");

$stmt->bind_param("i", $id);
$stmt->execute();

$row = $stmt->get_result()->fetch_assoc();

if (!$row) {

    header("Location: users.php");
    exit();

}


/*
|--------------------------------------------------------------------------
| Owner cannot change another owner's role
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name']);
    $role = $_POST['role'];

    if ($row['role'] === 'owner' && $id != $user['id']) {

        $role = 'owner';
    }

    /*
     * Never allow creating a second owner accidentally
     */

    if ($role === 'owner' && $id != $user['id']) {

        $role = 'staff';
    }


    $stmt = $conn->prepare("
        UPDATE users
        SET name = ?,
            role = ?
        WHERE id = ?
    ");

    $stmt->bind_param(
        "ssi",
        $name,
        $role,
        $id
    );

    $stmt->execute();

    header("Location: users.php");
    exit();
}

?>

<!DOCTYPE html>
<html>

<head>

<title>Edit User</title>

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

<h1>Edit User</h1>

</div>

<div class="dashboard-panel">

<form method="POST"
class="admin-form">

<div class="form-group">

<label>Name</label>

<input type="text"
name="name"
value="<?= htmlspecialchars($row['name']) ?>"
required>

</div>


<div class="form-group">

<label>Email</label>

<input type="text"
value="<?= htmlspecialchars($row['email']) ?>"
disabled>

</div>


<div class="form-group">

<label>Role</label>

<select name="role">

<option value="customer"
<?= $row['role'] === 'customer' ? 'selected' : '' ?>>

Customer

</option>

<option value="staff"
<?= $row['role'] === 'staff' ? 'selected' : '' ?>>

Staff

</option>

<?php if ($id == $user['id']): ?>

<option value="owner"
selected>

Owner

</option>

<?php endif; ?>

</select>

</div>


<div class="form-actions">

<button class="admin-button">

Save Changes

</button>

<a href="users.php"
class="secondary-button">

Cancel

</a>

</div>

</form>

</div>

</main>

</div>

</body>

</html>