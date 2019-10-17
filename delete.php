<?php
    session_start();
    require_once "pdo.php";

    //back to index on cancel click
    if (isset($_POST['cancel'])) {
        header("Location: index.php");
        return;
    }

    //deletes entry in db on delete click
    if (isset($_POST['delete']) && isset($_POST["profile_id"])) {
        $stmt = $pdo->prepare("DELETE FROM Profile WHERE profile_id = :pid");
        $stmt->execute(array(':pid' => $_POST['profile_id']));
        $_SESSION['success'] = "Record deleted";
        header("Location: index.php");
        return;
    }

    //get info on this entry from db for display
    $stmt = $pdo->prepare("SELECT first_name, last_name, profile_id FROM Profile WHERE profile_id = :pid");
    $stmt->execute(array(":pid" => $_GET['profile_id'] ));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row === false) {
        $_SESSION['error'] = "Bad value for profile_id";
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
        <h1>Deleting Profile</h1>
        <p>First Name: <?= $row['first_name'] ?></p>
        <p>Last Name: <?= $row['last_name'] ?></p>
        <form method="POST">
            <input type="hidden" name="profile_id" value="<?= $row['profile_id']?>">
            <input type="submit" name="delete" value="Delete">
            <input type="submit" name="cancel" value="cancel">
        </form>
    </body>
</html>