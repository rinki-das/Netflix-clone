<?php
session_start();
if (isset($_SESSION['loggedInUsername'])) {
    echo $_SESSION['loggedInUsername'];
}
?>
