<?php
$file = 'perguntas.json';
if (!file_exists($file)) {
    $perguntas = [
        "101" => "Qual é a capital do Brasil?",
        "102" => "Quem descobriu o Brasil?",
        "103" => "Quantos planetas existem no sistema solar?"
    ];
    file_put_contents($file, json_encode($perguntas,JSON_PRETTY_PRINT));
}

$perguntas = json_decode(file_get_contents($file), true);

$input = json_decode(file_get_contents('php://input'), true);

$acao = $input['acao'] ?? '';
$codigo = trim($input['codigo'] ?? '');
$novaPergunta = trim($input['pergunta'] ?? '');

if ($acao === 'buscar') {
    if (isset($perguntas[$codigo])) {
        echo json_encode([
            'sucesso' => true,
            'pergunta' => $perguntas[$codigo]
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
    if (isset($perguntas[$codigo])) {
        $perguntas[$codigo] = $novaPergunta;
        
        file_put_contents($file, json_encode($perguntas,JSON_PRETTY_PRINT));

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
