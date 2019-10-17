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
        <h1>Alessandro Allegranzi's Resume Registry</h1>
        <?php
            //check for session errors or successes
            if ( isset($_SESSION['error']) ) {
                echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
                unset($_SESSION['error']);
            }
            if ( isset($_SESSION['success']) ) {
                echo '<p style="color:green">'.$_SESSION['success']."</p>\n";
                unset($_SESSION['success']);
            }

            //dislplays log in or logout depending on current sessions as well as table info
            if (!isset($_SESSION["name"])) {
                echo('<a href="login.php">Please log in</a>');
                if (count($rows) > 0) {
                    echo('<div>');
                    echo('<table border="1">');
                        echo('<tr>');
                        echo('<th>Name</th>');
                        echo('<th>Headline</th>');
                        echo('</tr>');
                        foreach($rows as $row) {
                            echo'<tr><td>';
                            echo($row['first_name']. $row['last_name']);
                            echo '</td><td>';
                            echo($row["headline"]);
                            echo '</td><tr>';
                        }
                    echo '</table>';
                }
            } else {
                echo('<a href="logout.php">Logout</a></br>');
                if (count($rows) > 0) {
                    echo('<div>');
                    echo('<table border="1">');
                        echo('<tr>');
                        echo('<th>Name</th>');
                        echo('<th>Headline</th>');
                        echo('<th>Action</th>');
                        echo('</tr>');
                        foreach($rows as $row) {
                            echo'<tr><td>';
                            echo($row['first_name']." ". $row['last_name']);
                            echo '</td><td>';
                            echo($row["headline"]);
                            echo '</td><td>';
                            echo('<a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a> / ');
                            echo('<a href="delete.php?profile_id='.$row['profile_id'].'">Delete</a>');
                            echo "</td><tr>";
                        }
                    echo '</table>';
                }
                echo('<a href="add.php">Add New Entry</a>');
            }

            
        ?>
    </body>
</html>
