<?php
    require_once "pdo.php";

    header('Content-Type: application/json; charset=utf-8');
    $stmt = $pdo->prepare('SELECT name FROM Institution WHERE name LIKE :prefix');
    $stmt->execute(array(':prefix' => $_REQUEST['term']."%"));
    $return_value = array();
    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
        $return_value[] = $row['name'];
    }

    echo(json_encode($return_value, JSON_PRETTY_PRINT));
?>