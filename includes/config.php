<?php
// Configurações do banco de dados
$host = "localhost";
$user = "root";
$pass = "";
$db   = "tcc";

// Conexão
$conexao = mysqli_connect($host, $user, $pass, $db);

// Evita warnings/notices que quebram o JSON
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

// Não envia JSON automaticamente em erro de conexão
if (!$conexao) {
    // Apenas retorna falso para o chamador tratar
    die('Erro na conexão com o banco: '.mysqli_connect_error());
}
?>
