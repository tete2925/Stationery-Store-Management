<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


/*
|--------------------------------------------------------------------------
| Database connection
|--------------------------------------------------------------------------
*/

require_once __DIR__ . "/../includes/db.php";


/*
|--------------------------------------------------------------------------
| Check whether user is logged in
|--------------------------------------------------------------------------
*/

if (
    !isset($_SESSION['user_id']) &&
    !isset($_SESSION['user_email'])
) {
    header("Location: ../auth/login.php");
    exit();
}


/*
|--------------------------------------------------------------------------
| Check database connection
|--------------------------------------------------------------------------
*/

if (!isset($conn) || !$conn) {
    die("Database connection failed. Please check includes/db.php.");
}


/*
|--------------------------------------------------------------------------
| Get current user
|--------------------------------------------------------------------------
*/

$user = null;


/*
|--------------------------------------------------------------------------
| Get user by ID
|--------------------------------------------------------------------------
*/

if (isset($_SESSION['user_id'])) {

    $user_id = intval($_SESSION['user_id']);

    $stmt = $conn->prepare("
        SELECT
            id,
            name,
            email,
            role
        FROM users
        WHERE id = ?
        LIMIT 1
    ");

    if ($stmt) {

        $stmt->bind_param(
            "i",
            $user_id
        );

        $stmt->execute();

        $result = $stmt->get_result();

        if ($result) {
            $user = $result->fetch_assoc();
        }

        $stmt->close();
    }
}


/*
|--------------------------------------------------------------------------
| Fallback: Get user by email
|--------------------------------------------------------------------------
*/

if (
    !$user &&
    isset($_SESSION['user_email'])
) {

    $email = $_SESSION['user_email'];

    $stmt = $conn->prepare("
        SELECT
            id,
            name,
            email,
            role
        FROM users
        WHERE email = ?
        LIMIT 1
    ");

    if ($stmt) {

        $stmt->bind_param(
            "s",
            $email
        );

        $stmt->execute();

        $result = $stmt->get_result();

        if ($result) {
            $user = $result->fetch_assoc();
        }

        $stmt->close();
    }
}


/*
|--------------------------------------------------------------------------
| User not found
|--------------------------------------------------------------------------
*/

if (
    !$user ||
    !is_array($user)
) {

    session_unset();
    session_destroy();

    header("Location: ../auth/login.php");
    exit();
}


/*
|--------------------------------------------------------------------------
| Only Owner and Staff can access admin
|--------------------------------------------------------------------------
*/

if (
    $user['role'] !== 'owner' &&
    $user['role'] !== 'staff'
) {

    header("Location: ../index.php");
    exit();
}


/*
|--------------------------------------------------------------------------
| Store current user information in session
|--------------------------------------------------------------------------
*/

$_SESSION['user_id'] = $user['id'];

$_SESSION['user_name'] = $user['name'];

$_SESSION['user_email'] = $user['email'];

$_SESSION['user_role'] = $user['role'];


/*
|--------------------------------------------------------------------------
| Check permission
|--------------------------------------------------------------------------
*/

function has_permission(string $permission): bool
{

    global $user;


    /*
    |--------------------------------------------------------------------------
    | Safety check
    |--------------------------------------------------------------------------
    */

    if (
        !isset($user) ||
        !is_array($user) ||
        !isset($user['role'])
    ) {

        return false;
    }


    /*
    |--------------------------------------------------------------------------
    | OWNER
    |--------------------------------------------------------------------------
    |
    | Owner can access everything.
    |
    */

    if ($user['role'] === 'owner') {

        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | STAFF
    |--------------------------------------------------------------------------
    |
    | Staff can access all STORE MANAGEMENT sections.
    |
    | User management is NOT included here.
    |
    */

    if ($user['role'] === 'staff') {

        $staff_permissions = [

            'categories',

            'products',

            'inventory',

            'stock_in',

            'stock_out',

            'suppliers',

            'orders'

        ];


        return in_array(
            $permission,
            $staff_permissions,
            true
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Other roles
    |--------------------------------------------------------------------------
    */

    return false;
}


/*
|--------------------------------------------------------------------------
| Require permission
|--------------------------------------------------------------------------
*/

function require_permission(string $permission): void
{

    if (!has_permission($permission)) {

        header("Location: index.php");
        exit();
    }
}

?>