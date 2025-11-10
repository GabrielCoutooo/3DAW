<?php
$servidor = "localhost";
$username = "root";
$senha = "";
$database = "gestaoJogoDB";

$conn = new mysqli($servidor, $username, $senha, $database);
if ($conn->connect_error) {
    die("Conexão falhou!");
}
?>