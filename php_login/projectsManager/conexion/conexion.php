<?php
$servidor = "mysql:dbname=logintoyota;host=127.0.0.1";
$usuario = "root";
$password = "ra1zc0mpleja";

try{
    $pdo = new PDO($servidor,$usuario,$password,array(PDO::MYSQL_ATTR_INIT_COMMAND=>"SET NAMES utf8"));
    // echo "Conectado...";
}catch(PDOException $e){
    echo "Error de conexion".$e->getMessage();
}

?>