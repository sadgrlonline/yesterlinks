<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<nav>
    <ul>
        <li><a href="https://yesterweb.org">Yesterweb.org</a></li>
        <li><a href="/">Directory</a></li>
        <li><a href="/submit-a-link.php">Submit</a></li>
        <?php
        /* If someone has logged in with the `.htpasswd` credentials, this item will show on the Admin page */
        if (isset($_SESSION['username'])) {
          ?>
          <li><a href="/admin">Admin</a></li>
          <li><a href="/reports">Reports</a></li>
          <li><a href="../login?action=logout">Logout</a></li>
        <?php } ?>
    </ul>
</nav>
