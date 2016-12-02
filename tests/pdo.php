<?php

$dsn = 'mysql:host=127.0.0.1;dbname=phosphorum';
$user = 'phosphorum';
$password = 'secret';

try {
    $dbh = new PDO($dsn, $user, $password);

    $sql = 'SELECT * FROM users ORDER BY name';
    foreach ($dbh->query($sql) as $row) {
        echo json_encode($row, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;
    }


} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
