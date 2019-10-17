<?php
    //standard function to start session
    session_start();
    require_once "pdo.php";
    
    //cancel button logic to return to index
    if(isset($_POST["cancel"])) {
        header('Location: index.php');
        return;
    }

    //login logic
    if (isset($_POST['email']) && isset($_POST['pass'])) {
        if (strlen($_POST['email']) < 1 || strlen($_POST['pass']) < 1) {
            $_SESSION['error'] = "Email and password and required";
            header("Location: login.php");
            return;
        } else if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "Email must have an at-sign (@)";
            header("Location: login.php");
            return;
        } else {
            //slating password and checking to see if it exists in the DB
            unset($_SESSION["name"]);
            unset($_SESSION["user_id"]);
            $salt = 'XyZzy12*_';
            $check = hash('md5', $salt.$_POST['pass']);
            $stmt = $pdo->prepare('SELECT user_id, name FROM users
            WHERE email = :em AND password = :pw');
            $stmt->execute(array( ':em' => $_POST['email'], ':pw' => $check));
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            //see if record exists and if it does set session
            if ( $row !== false ) {
                $_SESSION['success'] = "Logged In";
                $_SESSION['name'] = $row['name'];
                $_SESSION['user_id'] = $row['user_id'];
                
                // Redirect the browser to index.php
                header("Location: index.php");
                return;
            } else {
                //set error if record not found
                $_SESSION['error'] = "Could not find user";
                header("Location: login.php");
                return;
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Alessandro Allegranzi - Resume Registry</title>
        <?php require_once 'bootstrap_styling.php' ?>
    </head>
    <body>
        <script>
            //JS validation for input fields
            function doValidate() {
                console.log('Validating...');
                const em = document.getElementById('email').value;
                const pw = document.getElementById('pass').value;
                try {
                    console.log("Validating pw="+pw);
                    if (em == null || em == "" || pw == null || pw == "") {
                        alert("Both fields must be filled out");
                        return false;
                    }
                    if (em.indexOf('@') == -1) {
                        alert("Invalid email address");
                        return false;
                    }
                    return true;
                    } catch(e) {
                        return false;
                    }
                return false;
            }
        </script>
        <h1>Please Log In</h1>
        <?php 
            //checking to see if there is an error in session and if there is displays it and unsets the error for another try
            if (isset($_SESSION['error'])) {
                echo('<p style="color: red;">'.htmlentities($_SESSION['error'])."</p>\n");
                unset($_SESSION['error']);
            }
        ?>
        <form method="POST">
            <label for="email">Email</label>
            <input type="text" name="email" id="email">
            <label for="pass">Password</label>
            <input type="text" name="pass" id="pass">
            <input type="submit" onclick="return doValidate();" value="Log In">
            <input type="submit" name="cancel" value="Cancel">
        </form>
    </body>
</html>