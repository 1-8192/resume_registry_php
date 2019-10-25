<?php
    session_start();
    require_once "pdo.php";
    require_once "utility_functions.php";

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
       
        //checking form data
        $msg = validateProfile();
        if (is_string($msg)) {
            $_SESSION['error'] = $msg;
            header("Location: add.php");
            return;
        }

        //validating position
        $msg = validatePos();
        if (is_string($msg)) {
            $_SESSION['error'] = $msg;
            header("Location: add.php");
            return;
        }

        //validating education
        $msg = validateEdu();
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

          //Posting new positions if they're there
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

          //Posting education
          $rank = 1;

          for ($i=1; $i<=9; $i++) {
              if (! isset($_POST['edu_year'.$i]) ) continue;
              if (! isset($_POST['edu_school'.$i]) ) continue;
              
              $year = $_POST['edu_year'.$i];
              $school = $_POST['edu_school'.$i];
              
              //check to see if school already exists
              $institution_id = false;
              $stmt = $pdo->prepare('SELECT institution_id FROM Institution WHERE name = :name');
              $stmt->execute(array(':name' => $school));
              $row = $stmt->fetch(PDO::FETCH_ASSOC);
              if ( $row !== false ) $institution_id = $row['institution_id'];

              //if no institution was found insert new one
              if ($institution_id === false) {
                  $stmt = $pdo->prepare('INSERT INTO Institution (name) VALUES (:name)');
                  $stmt->execute(array(':name' => $school));
                  $institution_id = $pdo->lastInsertId();
              }

              //Insert with existing school
              $stmt= $pdo->prepare('INSERT INTO Education (profile_id, rank, year, institution_id) VALUES (:pid, :rank, :year, :iid)');
              $stmt->execute(array(
                  ':pid' => $profile_id,
                  ':rank' => $rank,
                  ':year' => $year,
                  ':iid' => $institution_id
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
            <p>education:</p>
            <input type="submit" id="education-add" value="+">
            <div id="educations"></div>
            <script>
                //jquery logic for adding up to 9 position fields to form
                countPos = 0;
                countEdu = 0;

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

                    $('#education-add').click(function(event) {
                        event.preventDefault();
                        if (countEdu >= 9) {
                            alert('Maximum of nine entries exceeded');
                            return;
                        }
                        countEdu++;

                        window.console && console.log("Adding education " + countEdu);
                        $('#educations').append(
                            '<div id="education'+countEdu+'"> \
                            <p>Year: <input type="text" name="edu_year'+countEdu+'" value=""> \
                            <input type="button" value="-" \
                                onClick="$(\'#education'+countEdu+'\').remove();return false;"></p>\
                                <p>School: <input type="text" class="school" name="edu_school'+countEdu+'" size="80"></p>\
                                </div>');
                                $('.school').autocomplete({ source: "school.php" });
                    });
                });
            </script>
            <input type="submit" value="Add">
            <input type="submit" name="cancel" value="Cancel">
        </form>
    </body>
</html>