<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'crud_alunos';

try {
    $conexao = new mysqli($host, $user, $password, $database);
    
    if ($conexao->connect_error) {
        throw new Exception('Erro na conexão: ' . $conexao->connect_error);
    }
    
    $conexao->set_charset('utf8');
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['erro' => $e->getMessage()]);
    exit;
}
?>
