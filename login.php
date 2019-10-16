<?php
    session_start();
    if(isset($_POST["cancel"])) {
        header('Location: index.php');
        return;
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Alessandro Allegranzi - Resume Registry</title>
        <?php require_once 'bootstrap_styling.php' ?>
    </head>
    <body>
        <h1>Login</h1>
    </body>
</html>