<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!empty($_SESSION['username'])) {
  header("location: ../admin");
}

if (isset($_GET['action']) && $_GET['action'] === "logout") {
  session_destroy();
  header("location: /yesterlinks");
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="../assets/css/style.css" media="all">
    <title>Yesterlinks | Login</title>
</head>
    <body>
        <div class="container">
            <div class="wrapper">
    <div class="form">
        <form method="post" action="login.php">
                <h1>Login</h1>
                <div class="form-group">
                    <div class="form-group">
                        <input type="text" class="textbox" id="username" name="username" placeholder="Username" />
                    </div>
                    <div class="form-group">
                        <input type="password" class="textbox" id="password" name="password" placeholder="Password" />
                    </div>
                    </p>
                    <div>
                        <input type="submit" value="Submit" name="but_submit" id="but_submit" />
                </div>
        </form>
    </div>
</div>
</body>

</html>
