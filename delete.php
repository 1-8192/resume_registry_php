<?php
    session_start();
    require_once "pdo.php";

    //deletes entry in db on delete click
    if (isset($_POST['delete']) && isset($_POST["profile_id"])) {
        $stmt = $pdo->prepare("DELETE FROM Profile WHERE profile_id = :pid");
        $stmt->execute(array(':pid' => $_POST['profile_id']));
        $_SESSION['success'] = "Record deleted";
        header("Location: index.php");
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
    </body>
</html>