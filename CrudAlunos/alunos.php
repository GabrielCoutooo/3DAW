<?php
header('Content-Type: application/json;');

include 'config.php';

$acao = $_GET['acao'] ?? '';

try {
    switch ($acao) {
        case 'listar':
            listarAlunos();
            break;
        
        case 'criar':
            criarAluno();
            break;
        
        case 'atualizar':
            atualizarAluno();
            break;
        
        case 'deletar':
            deletarAluno();
            break;
        
        default:
            http_response_code(400);
            echo json_encode(['erro' => 'Ação não especificada']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['erro' => $e->getMessage()]);
}

function listarAlunos() {
    global $conexao;
    
    $sql = "SELECT id, nome, matricula, email FROM alunos";
    $resultado = $conexao->query($sql);
    
    if (!$resultado) {
        throw new Exception('Erro na consulta: ' . $conexao->error);
    }
    
    $alunos = [];
    while ($linha = $resultado->fetch_assoc()) {
        $alunos[] = $linha;
    }
    
    echo json_encode($alunos);
}

function criarAluno() {
    global $conexao;
    
    $dados = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($dados['nome']) || !isset($dados['matricula']) || !isset($dados['email'])) {
        http_response_code(400);
        throw new Exception('Dados incompletos');
    }
    
    $nome = $conexao->real_escape_string($dados['nome']);
    $matricula = $conexao->real_escape_string($dados['matricula']);
    $email = $conexao->real_escape_string($dados['email']);
    
    if (empty($nome) || empty($matricula) || empty($email)) {
        http_response_code(400);
        throw new Exception('Campos não podem estar vazios');
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        throw new Exception('Email inválido');
    }
    
    $sqlVerifica = "SELECT id FROM alunos WHERE matricula = '$matricula'";
    $resultado = $conexao->query($sqlVerifica);
    
    if ($resultado->num_rows > 0) {
        http_response_code(400);
        throw new Exception('Matrícula já existe');
    }
    
    $sql = "INSERT INTO alunos (nome, matricula, email) VALUES ('$nome', '$matricula', '$email')";
    
    if (!$conexao->query($sql)) {
        throw new Exception('Erro ao inserir: ' . $conexao->error);
    }
    
    http_response_code(201);
    echo json_encode([
        'id' => $conexao->insert_id,
        'nome' => $dados['nome'],
        'matricula' => $dados['matricula'],
        'email' => $dados['email'],
        'mensagem' => 'Aluno criado com sucesso'
    ]);
}

function atualizarAluno() {
    global $conexao;
    
    $dados = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($dados['id']) || !isset($dados['nome']) || !isset($dados['matricula']) || !isset($dados['email'])) {
        http_response_code(400);
        throw new Exception('Dados incompletos');
    }
    
    $id = intval($dados['id']);
    $nome = $conexao->real_escape_string($dados['nome']);
    $matricula = $conexao->real_escape_string($dados['matricula']);
    $email = $conexao->real_escape_string($dados['email']);
    
    if (empty($nome) || empty($matricula) || empty($email)) {
        http_response_code(400);
        throw new Exception('Campos não podem estar vazios');
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        throw new Exception('Email inválido');
    }
    
    $sqlVerifica = "SELECT id FROM alunos WHERE matricula = '$matricula' AND id != $id";
    $resultado = $conexao->query($sqlVerifica);
    
    if ($resultado->num_rows > 0) {
        http_response_code(400);
        throw new Exception('Matrícula já existe em outro aluno');
    }
    
    $sql = "UPDATE alunos SET nome = '$nome', matricula = '$matricula', email = '$email' WHERE id = $id";
    
    if (!$conexao->query($sql)) {
        throw new Exception('Erro ao atualizar: ' . $conexao->error);
    }
    
    if ($conexao->affected_rows === 0) {
        http_response_code(404);
        throw new Exception('Aluno não encontrado');
    }
    
    echo json_encode([
        'id' => $id,
        'nome' => $dados['nome'],
        'matricula' => $dados['matricula'],
        'email' => $dados['email'],
        'mensagem' => 'Aluno atualizado com sucesso'
    ]);
}

function deletarAluno() {
    global $conexao;
    
    $id = intval($_GET['id'] ?? 0);
    
    if ($id <= 0) {
        http_response_code(400);
        throw new Exception('ID inválido');
    }
    
    $sql = "DELETE FROM alunos WHERE id = $id";
    
    if (!$conexao->query($sql)) {
        throw new Exception('Erro ao deletar: ' . $conexao->error);
    }
    
    if ($conexao->affected_rows === 0) {
        http_response_code(404);
        throw new Exception('Aluno não encontrado');
    }
    
    echo json_encode(['mensagem' => 'Aluno deletado com sucesso']);
}
?>
