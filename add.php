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

    //Validate profile fieilds
    function validateProfile() {
        if (strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1) {
            return "All fields are required";
        } else if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            return "Email address must contain @";
        }
        return true;
    }

    //Validate position fields
    function validatePos() {
        for($i=1; $i<=9; $i++) {
          if ( ! isset($_POST['year'.$i]) ) continue;
          if ( ! isset($_POST['desc'.$i]) ) continue;
      
          $year = $_POST['year'.$i];
          $desc = $_POST['desc'.$i];
      
          if ( strlen($year) == 0 || strlen($desc) == 0 ) {
            return "All fields are required";
          }
      
          if ( ! is_numeric($year) ) {
            return "Position year must be numeric";
          }
        }
        return true;
      }

    function validateEdu() {
        for($i=1; $i<=9; $i++) {
            if ( ! isset($_POST['year'.$i]) ) continue;
            if ( ! isset($_POST['desc'.$i]) ) continue;
        
            $year = $_POST['year'.$i];
            $desc = $_POST['desc'.$i];
        
            if ( strlen($year) == 0 || strlen($desc) == 0 ) {
              return "All fields are required";
            }
        
            if ( ! is_numeric($year) ) {
              return "Position year must be numeric";
            }
          }
          return true;
    }

    //PHP validation for input fields
    if (isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])) {
       
        //checking form data
        $msg = validateProfile();
        if (is_string($msg)) {
            $_SESSION['error'] = $msg;
            header("Location: add.php");
            return;
        }

        $msg = validatePos();
        if (is_string($msg)) {
            $_SESSION['error'] = $msg;
            header("Location: add.php");
            return;
        }

            //Posting new user to DB if form data is OK
            $stmt = $pdo->prepare('INSERT INTO Profile
            (user_id, first_name, last_name, email, headline, summary)
            VALUES ( :uid, :fn, :ln, :em, :he, :su)');
          
            $stmt->execute(array(
            ':uid' => $_SESSION['user_id'],
            ':fn' => $_POST['first_name'],
            ':ln' => $_POST['last_name'],
            ':em' => $_POST['email'],
            ':he' => $_POST['headline'],
            ':su' => $_POST['summary'])
          );

          $profile_id = $pdo->lastInsertId();

          $rank=1;

          for ($i=1; $i<=9; $i++) {
            if ( ! isset($_POST['year'.$i]) ) continue;
            if ( ! isset($_POST['desc'.$i]) ) continue;

            $stmt = $pdo->prepare('INSERT INTO Position (profile_id, rank, year, description) VALUES (:pid, :rank, :year, :desc)');
            $stmt->execute(array(
              ':pid' => $profile_id,
              ':rank' => $rank,
              ':year' => $_POST['year'.$i],
              ':desc' => $_POST['desc'.$i]
            ));
            $rank++;
          }

          $_SESSION['success'] = "Profile added";
          header("Location: index.php");
          return;
    }

?>

<!DOCTYPE html>
<html lang="en">
    <?php require_once "head.php" ?>
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
            <input type="text" name="first_name" size="60"></br>
            <label for="last_name">Last Name:</label>
            <input type="text" name="last_name" size="60"></br>
            <label for="email">Email:</label>
            <input type="text" name="email" size="30"></br>
            <label for="headline">Headline:</label>
            <input type="text" name="headline" size="80"></br>
            <label for="summary">Summary:</label>
            <textarea name="summary" rows="8" cols="80"></textarea></br>
            <p>Position:</p>
            <input type="submit" id="position-add" value="+">
            <div id="positions"></div>
            <script>
                //jquery logic for adding up to 9 position fields to form
                countPos = 0;

                $(document).ready(function() {
                    window.console && console.log('Document ready called');
                    $('#position-add').click(function(event) {
                        event.preventDefault();
                        if (countPos >= 9) {
                            alert('Maximum of nine entries exceeded');
                            return;
                        }
                        countPos++;
                        window.console && console.log("Adding position " + countPos);
                        $('#positions').append(
                            '<div id="position'+countPos+'"> \
                            <p>Year: <input type="text" name="year'+countPos+'" value=""> \
                            <input type="button" value="-" \
                                onClick="$(\'#position'+countPos+'\').remove();return false;"></p>\
                                <textarea name="desc'+countPos+'" rows="8" cols="88"></textarea>\
                                </div>');
                    });
                });
            </script>
            <input type="submit" value="Add">
            <input type="submit" name="cancel" value="Cancel">
        </form>
    </body>
</html>