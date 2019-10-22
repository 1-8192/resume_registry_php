<?php
    session_start();
    require_once "pdo.php";

    //getting data from server
    $stmt = $pdo->query("SELECT first_name, last_name, headline, profile_id FROM Profile ORDER BY last_name");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Alessandro Allegranzi - Resume Registry</title>
        <?php require_once 'bootstrap_styling.php' ?>
    </head>
    <body>
    </body>
</html>