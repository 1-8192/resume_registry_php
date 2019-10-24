<?php
    session_start();
    require_once "pdo.php";

     //logging logic
     if (!isset($_SESSION["name"])) {
        die("ACCESS DENIED");
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

     $stmt = $pdo->prepare("SELECT * FROM Position WHERE profile_id = :id");
     $stmt->execute(array(":id" => $_GET['profile_id']));
     $count = 0;
     $position_row = [];
     while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
         $position_row[$count] = $row;
         $count++;
     }

     $stmt = $pdo->prepare("SELECT year, name FROM Education JOIN Institution ON Education.institution_id = Institution.institution_id WHERE profile_id = :prof ORDER BY rank");
     $stmt->execute(array(":prof" => $_GET['profile_id']));
     $education_row = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
    <?php require_once "head.php" ?>
    <body>
     <div class="container">
        <h1>Profile information</h1>
        <p>First Name: <?php echo $first_name ?></p>
        <p>Last Name: <?php echo $last_name ?></p>
        <p>Email: <?php echo $email ?></p>
        <p>Headline: <?php echo $headline ?></p>
        <p>Summary: <?php echo $summary ?></p>
        <?php 
            if (count($position_row) > 0) {

                echo('<p>Positions:</p><ul>');
                
                    for($i=0; $i<count($position_row); $i++) {
                        $year = htmlentities($position_row[$i]['year']);
                        $desc = htmlentities($position_row[$i]['description']);

                        echo('<li>'.$year .': ' .$desc .'</li>');
                    } 
            }

            if (count($education_row) > 0) {

                echo('<p>Education:</p><ul>');

                for($i=0; $i<count($education_row); $i++) {
                    $year = htmlentities($education_row[$i]['year']);
                    $name = htmlentities($education_row[$i]['name']);
                    
                    echo('<li>'.$year.': ' .$name .'</li>');
                }   
            }
        ?>
        <a href="index.php">Done</a>
    </div>
    </body>
</html>