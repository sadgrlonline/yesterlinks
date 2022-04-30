<?php

// include
include "../config.php";

// runs when submit button is clicked
if (isset($_POST['but_submit'])) {

    $username = mysqli_real_escape_string($con, $_POST['username']);
    $password = mysqli_real_escape_string($con, $_POST['password']);

    $result = mysqli_fetch_assoc(mysqli_query($con, "SELECT password FROM users WHERE active = '1' AND username = '" . $username . "'"));
    $password_hash = (isset($result['password']) ? $result['password'] : '');
    $result = password_verify($password, $password_hash);

    if ($result) {
        session_start();
        $_SESSION['username'] = $username;
        header('Location: ../admin/');
    } else {
        $msg = "<div style='text-align:center; font-weight:bold; color:red; margin-bottom:10px;'> Login failed. Please make sure that you enter the correct details and that you have activated your account.</div>";
        echo $msg;
    }
}