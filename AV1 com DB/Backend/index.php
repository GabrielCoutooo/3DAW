<?php
require_once "conexao.php";
require_once "funcoes.php";

$entidade = $_GET['entidade'] ?? 'perguntas';
$acao = $_GET['acao'] ?? 'listar';
$id = $_GET['id'] ?? null;
$mensagem = '';
$erros = [];

if ($entidade === 'perguntas') {
    $perguntas = carregarPergunta($conn);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id_form = $_POST['id'] ?? null;
        $texto_pergunta = trim($_POST['texto_pergunta'] ?? '');
        $tipo_pergunta = $_POST['tipo_pergunta'] ?? '';

        if (empty($texto_pergunta)) $erros[] = 'O texto da pergunta é obrigatório!';
        if (empty($tipo_pergunta)) $erros[] = 'O tipo da pergunta é obrigatório';
        
        $respostas = [];
        if ($tipo_pergunta === 'multipla_escolha') {
            $opcoes = $_POST['opcoes'] ?? [];
            $vazias = 0;
            $preenchidas = [];
            $todas_vazias = true;
            
            foreach ($opcoes as $indice => $opcao_texto) {
                $texto_limpo = trim($opcao_texto);
                if ($texto_limpo === '') {
                    $vazias++;
                } else {
                    $todas_vazias = false;
                    $preenchidas[] = ['id' => count($preenchidas), 'respostas' => $texto_limpo];
                }
            }

            if ($todas_vazias) {
                $erros[] = 'É necessário preencher todas as 5 alternativas para perguntas de múltipla escolha';
            } else {
                for ($i = 0; $i < 5; $i++) {
                    $texto_alternativa = trim($opcoes[$i] ?? '');
                    if ($texto_alternativa === '') {
                        $erros[] = 'A alternativa ' . ($i + 1) . ' é obrigatória';
                    }
                }
            }

            if (count($preenchidas) >= 5) {
                $respostas = $preenchidas;
            }
        }

        if (empty($erros)) {
            if ($acao === 'criar') {
                criarPergunta($conn, $texto_pergunta, $tipo_pergunta, $respostas);
                echo json_encode(['mensagem' => 'Pergunta criada com sucesso!']);
            } else if ($acao === 'alterar' && $id_form) {
                alterarPergunta($conn, $id_form, $texto_pergunta, $tipo_pergunta, $respostas);
                echo json_encode(['mensagem' => 'Pergunta alterada com sucesso!']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['erros' => $erros]);
        }
        exit;
    }

    if ($acao === 'excluir' && $id) {
        excluirPergunta($conn, $id);
        echo json_encode(['mensagem' => 'Pergunta excluída com sucesso!']);
        exit;
    }

    if ($acao === 'listar') {
        echo json_encode($perguntas);
        exit;
    }
} elseif ($entidade === 'usuarios') {
    $usuarios = carregarUsuario($conn);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id_form = $_POST['id'] ?? null;
        $nome = trim($_POST['nome'] ?? '');
        $senha = $_POST['senha'] ?? '';
        
        if (empty($nome)) $erros[] = 'O nome do usuario é obrigatorio!';
        if ($acao === 'criar' && empty($senha)) {
            $erros[] = 'A senha é obrigatória para criar um novo usuário!';
        }

        if (empty($erros)) {
            $senha_hash = !empty($senha) ? password_hash($senha, PASSWORD_DEFAULT) : null;
            if ($acao === 'criar') {
                criarUsuario($conn, $nome, $senha_hash);
                echo json_encode(['mensagem' => 'Usuario criado com sucesso!']);
            } elseif ($acao === 'alterar' && $id_form) {
                alterarUsuario($conn, $id_form, $nome, $senha_hash);
                echo json_encode(['mensagem' => 'Usuario alterado com sucesso!']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['erros' => $erros]);
        }
        exit;
    }

    if ($acao === 'excluir' && $id) {
        excluirUsuario($conn, $id);
        echo json_encode(['mensagem' => 'Usuário excluído com sucesso!']);
        exit;
    }

    if ($acao === 'listar') {
        echo json_encode($usuarios);
        exit;
    }
}

http_response_code(400);
echo json_encode(['erro' => 'Operação inválida']);
?>