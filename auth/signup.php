<?php

session_start();

include "../includes/db.php";

$message = "";
$message_type = "";


/*
|--------------------------------------------------------------------------
| SIGN UP
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Get form data
    $name = trim($_POST["name"] ?? "");

    // Always store email in lowercase
    $email = strtolower(
        trim($_POST["email"] ?? "")
    );

    $password = $_POST["password"] ?? "";

    $confirm_password =
        $_POST["confirm_password"] ?? "";


    /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */

    if ($name === "") {

        $message = "Please enter your name.";
        $message_type = "error";

    }

    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";
        $message_type = "error";

    }

    elseif (strlen($password) < 6) {

        $message =
            "Password must be at least 6 characters.";

        $message_type = "error";

    }

    elseif ($password !== $confirm_password) {

        $message =
            "Passwords do not match.";

        $message_type = "error";

    }

    else {


        /*
        |--------------------------------------------------------------------------
        | CHECK DUPLICATE EMAIL
        |--------------------------------------------------------------------------
        */

        $check = $conn->prepare("
            SELECT id
            FROM users
            WHERE email = ?
            LIMIT 1
        ");

        $check->bind_param(
            "s",
            $email
        );

        $check->execute();

        $result = $check->get_result();


        if ($result->num_rows > 0) {

            $message =
                "An account with this email already exists.";

            $message_type = "error";

        }

        else {


            /*
            |--------------------------------------------------------------------------
            | HASH PASSWORD
            |--------------------------------------------------------------------------
            */

            $hashed_password =
                password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );


            /*
            |--------------------------------------------------------------------------
            | DEFAULT ROLE
            |--------------------------------------------------------------------------
            |
            | Anyone signing up from the public website
            | is automatically a CUSTOMER.
            |
            | They cannot choose:
            | staff
            | owner
            |
            */

            $role = "customer";


            /*
            |--------------------------------------------------------------------------
            | EMAIL VERIFICATION
            |--------------------------------------------------------------------------
            |
            | Email verification is currently disabled.
            | Therefore new accounts are immediately verified.
            |
            */

            $email_verified = 1;


            /*
            |--------------------------------------------------------------------------
            | CREATE ACCOUNT
            |--------------------------------------------------------------------------
            */

            $stmt = $conn->prepare("
                INSERT INTO users
                (
                    name,
                    email,
                    password,
                    role,
                    email_verified
                )
                VALUES (?, ?, ?, ?, ?)
            ");


            $stmt->bind_param(
                "ssssi",
                $name,
                $email,
                $hashed_password,
                $role,
                $email_verified
            );


            /*
            |--------------------------------------------------------------------------
            | SAVE ACCOUNT
            |--------------------------------------------------------------------------
            */

            if ($stmt->execute()) {


                /*
                |--------------------------------------------------------------------------
                | AUTOMATIC LOGIN
                |--------------------------------------------------------------------------
                */

                $user_id = $stmt->insert_id;


                session_regenerate_id(true);


                $_SESSION["user_id"] =
                    $user_id;

                $_SESSION["user_name"] =
                    $name;

                $_SESSION["user_email"] =
                    $email;

                $_SESSION["user_role"] =
                    "customer";


                /*
                |--------------------------------------------------------------------------
                | GO TO CLIENT WEBSITE
                |--------------------------------------------------------------------------
                */

                header(
                    "Location: ../index.php"
                );

                exit;

            }

            else {

                /*
                | If MySQL UNIQUE constraint catches
                | a duplicate email
                */

                if (
                    $conn->errno == 1062
                ) {

                    $message =
                        "An account with this email already exists.";

                }

                else {

                    $message =
                        "Something went wrong while creating your account.";
                }

                $message_type = "error";
            }

        }

    }

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Create Account - Student Stationery
    </title>


    <!-- FONT AWESOME -->

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    >


    <!-- AUTH CSS -->

    <link
        rel="stylesheet"
        href="auth.css"
    >

</head>


<body>


<div class="auth-page">


    <div class="auth-card">


        <!-- LOGO -->

        <a
            href="../index.php"
            class="auth-logo"
        >

            <div class="auth-logo-icon">

                <i class="fa-solid fa-pencil"></i>

            </div>


            <div>

                Student
                <strong>Stationery</strong>

            </div>

        </a>



        <!-- HEADER -->

        <div class="auth-header">

            <p class="auth-label">
                CREATE ACCOUNT
            </p>


            <h1>
                Sign Up
            </h1>


            <p>
                Create your Student Stationery account.
            </p>

        </div>



        <!-- ERROR -->

        <?php if ($message !== ""): ?>

            <div
                class="auth-message
                <?php echo htmlspecialchars($message_type); ?>"
            >

                <?php
                echo htmlspecialchars($message);
                ?>

            </div>

        <?php endif; ?>



        <!-- SIGN UP FORM -->

        <form
            method="POST"
            class="auth-form"
        >


            <!-- NAME -->

            <div class="form-group">

                <label for="name">
                    Name
                </label>


                <div class="input-wrapper">

                    <i class="fa-solid fa-user"></i>


                    <input
                        type="text"
                        id="name"
                        name="name"
                        placeholder="Enter your name"
                        value="<?php
                        echo htmlspecialchars(
                            $_POST["name"] ?? ""
                        );
                        ?>"
                        required
                        autocomplete="name"
                    >

                </div>

            </div>



            <!-- EMAIL -->

            <div class="form-group">

                <label for="email">
                    Email
                </label>


                <div class="input-wrapper">

                    <i class="fa-solid fa-envelope"></i>


                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Enter your email"
                        value="<?php
                        echo htmlspecialchars(
                            $_POST["email"] ?? ""
                        );
                        ?>"
                        required
                        autocomplete="email"
                    >

                </div>

            </div>



            <!-- PASSWORD -->

            <div class="form-group">

                <label for="password">
                    Password
                </label>


                <div class="input-wrapper">

                    <i class="fa-solid fa-lock"></i>


                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="At least 6 characters"
                        required
                        autocomplete="new-password"
                    >

                </div>

            </div>



            <!-- CONFIRM PASSWORD -->

            <div class="form-group">

                <label for="confirm_password">
                    Confirm Password
                </label>


                <div class="input-wrapper">

                    <i class="fa-solid fa-lock"></i>


                    <input
                        type="password"
                        id="confirm_password"
                        name="confirm_password"
                        placeholder="Confirm your password"
                        required
                        autocomplete="new-password"
                    >

                </div>

            </div>



            <!-- SUBMIT -->

            <button
                type="submit"
                class="auth-button"
            >

                Create Account

                <i class="fa-solid fa-arrow-right"></i>

            </button>


        </form>



        <!-- LOGIN LINK -->

        <div class="auth-footer">

            Already have an account?

            <a href="login.php">
                Login
            </a>

        </div>


    </div>

</div>


</body>

</html>