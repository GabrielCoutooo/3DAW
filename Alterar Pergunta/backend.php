<?php
session_start();

if (!isset($_SESSION['bd'])) {
    $_SESSION['bd'] = [
        "101" => "Qual é a capital do Brasil?",
        "102" => "Quem descobriu o Brasil?",
        "103" => "Quantos planetas existem no sistema solar?"
    ];
}

$input = json_decode(file_get_contents('php://input'), true);

$acao = $input['acao'] ?? '';
$codigo = trim($input['codigo'] ?? '');
$novaPergunta = trim($input['pergunta'] ?? '');

if ($acao === 'buscar') {
    if (isset($_SESSION['bd'][$codigo])) {
        echo json_encode([
            'sucesso' => true,
            'pergunta' => $_SESSION['bd'][$codigo]
        ]);
    } else {
        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Pergunta não encontrada.'
        ]);
    }
    exit;
}

if ($acao === 'salvar') {
    if (isset($_SESSION['bd'][$codigo])) {
        $_SESSION['bd'][$codigo] = $novaPergunta;
        echo json_encode([
            'sucesso' => true,
            'mensagem' => 'Pergunta atualizada com sucesso.'
        ]);
    } else {
        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Código inválido. Não foi possível atualizar.'
        ]);
    }
    exit;
}

echo json_encode([
    'sucesso' => false,
    'mensagem' => 'Ação inválida.'
]);