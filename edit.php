<?php
    session_start();
    require_once "pdo.php";

    //logging logic
    if (!isset($_SESSION["name"])) {
        die("ACCESS DENIED");
    }

    if (isset($_POST["cancel"])) {
        header('Location: index.php');
        return;
    }

    //PHP validation for input fields
    if (isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])) {
        if (strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1) {
            $_SESSION['error'] = "All fields are required";
            header("Location: edit.php?profile_id=".$_POST['profile_id']);
            return;
        } else if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "Email address must contain @";
            header("Location: edit.php?profile_id=".$_POST['profile_id']);
            return;
        } else {
            //Posting new user to DB
            $stmt = $pdo->prepare('UPDATE Profile SET user_id = :uid, first_name = :fn, last_name = :ln, email = :em, headline = :he, summary = :su WHERE profile_id = :pid');

            $stmt->execute(array(
            ':uid' => $_SESSION['user_id'],
            ':fn' => $_POST['first_name'],
            ':ln' => $_POST['last_name'],
            ':em' => $_POST['email'],
            ':he' => $_POST['headline'],
            ':su' => $_POST['summary'],
            ':pid' => htmlentities($_POST['profile_id'])
          ));
          $_SESSION['success'] = "Profile saved";
          header("Location: index.php");
          return;
        }
    }

    //Getting data for user to edit from db
    $stmt = $pdo->prepare("SELECT * FROM Profile WHERE profile_id = :id");
    $stmt->execute(array(":id" => $_GET['profile_id']));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    //making sure we're getting back an answer
    if ($row === false) {
        $_SESSION['error'] = "Bad value for profile id";
        header("Location: index.php");
        return;
    } else {
        $first_name = htmlentities($row['first_name']);
        $last_name = htmlentities($row['last_name']);
        $email = htmlentities($row['email']);
        $headline = htmlentities($row['headline']);
        $summary = htmlentities($row['summary']);
        $profile_id = htmlentities($row['profile_id']);
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Alessandro Allegranzi - Resume Registry</title>
        <?php require_once 'bootstrap_styling.php' ?>
        <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>
    </head>
    <body>
        <h1> Adding Profile for <?php echo(htmlentities($_SESSION['name'])) ?> </h1>
        <?php 
            //check for error in sessions
            if (isset($_SESSION['error'])) {
                echo('<p style="color: red;">'.htmlentities($_SESSION['error'])."</p>\n");
                 unset($_SESSION['error']);
            }
        ?>
        <form method="POST">
            <label for="first_name">First Name:</label>
            <input type="text" name="first_name" size="60" value="<?= $first_name ?>"></br>
            <label for="last_name">Last Name:</label>
            <input type="text" name="last_name" size="60" value="<?= $last_name ?>"></br>
            <label for="email">Email:</label>
            <input type="text" name="email" size="30" value="<?= $email ?>"></br>
            <label for="headline">Headline:</label>
            <input type="text" name="headline" size="80" value="<?= $headline ?>"></br>
            <label for="summary">Summary:</label>
            <textarea name="summary" rows="8" cols="80"><?= $summary ?></textarea></br>
            <input type="hidden" name="profile_id" value="<?= $profile_id?>">
            <input type="submit" value="Save">
            <input type="submit" name="cancel" value="Cancel">
        </form>
    </body>
</html>