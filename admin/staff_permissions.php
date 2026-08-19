<?php

require_once "admin_auth.php";

if ($user['role'] !== 'owner') {

    header("Location: index.php");
    exit();

}

$staff_id = intval($_GET['id'] ?? 0);

$stmt = $conn->prepare("
    SELECT id, name, email, role
    FROM users
    WHERE id = ?
");

$stmt->bind_param("i", $staff_id);
$stmt->execute();

$staff = $stmt->get_result()->fetch_assoc();

if (!$staff || $staff['role'] !== 'staff') {

    header("Location: users.php");
    exit();

}


$permissions = [

    'products' => 'Products',
    'categories' => 'Categories',
    'inventory' => 'Inventory',
    'stock_in' => 'Stock In',
    'stock_out' => 'Stock Out',
    'suppliers' => 'Suppliers',
    'orders' => 'Orders'

];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $selected = $_POST['permissions'] ?? [];


    /*
     * Remove old permissions
     */

    $stmt = $conn->prepare("
        DELETE FROM staff_permissions
        WHERE user_id = ?
    ");

    $stmt->bind_param("i", $staff_id);
    $stmt->execute();


    /*
     * Add selected permissions
     */

    foreach ($selected as $permission) {

        if (!array_key_exists($permission, $permissions)) {
            continue;
        }

        $stmt = $conn->prepare("
            INSERT INTO staff_permissions
            (user_id, permission)
            VALUES (?, ?)
        ");

        $stmt->bind_param(
            "is",
            $staff_id,
            $permission
        );

        $stmt->execute();
    }


    header("Location: users.php");
    exit();
}


/*
|--------------------------------------------------------------------------
| Get existing permissions
|--------------------------------------------------------------------------
*/

$current = [];

$stmt = $conn->prepare("
    SELECT permission
    FROM staff_permissions
    WHERE user_id = ?
");

$stmt->bind_param("i", $staff_id);
$stmt->execute();

$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {

    $current[] = $row['permission'];
}

?>

<!DOCTYPE html>
<html>

<head>

<title>Staff Permissions</title>

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

<h1>Staff Permissions</h1>

</div>

</div>


<div class="dashboard-panel">

<p style="margin-bottom:25px;">

<strong>
<?= htmlspecialchars($staff['name']) ?>
</strong>

<br>

<?= htmlspecialchars($staff['email']) ?>

</p>


<form method="POST"
class="admin-form">


<?php foreach ($permissions as $key => $label): ?>

<div style="
    margin-bottom:15px;
    padding:14px;
    border:1px solid #ddd;
    border-radius:6px;
">

<label style="
    display:flex;
    align-items:center;
    gap:10px;
    font-size:14px;
">

<input type="checkbox"
name="permissions[]"
value="<?= $key ?>"
<?= in_array($key, $current) ? 'checked' : '' ?>>

<?= htmlspecialchars($label) ?>

</label>

</div>

<?php endforeach; ?>


<div class="form-actions">

<button type="submit"
class="admin-button">

Save Permissions

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