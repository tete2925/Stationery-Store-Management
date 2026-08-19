<?php

require_once "admin_auth.php";

if ($user['role'] !== 'owner') {

    header("Location: index.php");
    exit();

}

$id = intval($_GET['id'] ?? 0);


/*
|--------------------------------------------------------------------------
| Never allow owner to delete themselves
|--------------------------------------------------------------------------
*/

if ($id == $user['id']) {

    header("Location: users.php");
    exit();

}


/*
|--------------------------------------------------------------------------
| Never delete an owner
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    DELETE FROM users
    WHERE id = ?
    AND role != 'owner'
");

$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: users.php");
exit();

?>