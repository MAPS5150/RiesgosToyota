<?php
    $server = 'localhost';
    $username = 'root';
    $password = 'ra1zc0mpleja';
    $database = 'logintoyota';

    try{
        $conn = new PDO("mysql:host=$server; dbname=$database;", $username, $password);
        echo 'Conexión exitosa a la BD';
    } catch (PDOException $e) {
        die('Error de conexión: '.$e->getMessage());
    }
?>