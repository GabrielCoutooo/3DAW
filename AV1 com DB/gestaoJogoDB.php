<?php
$servidor = "localhost";
$username = "root";
$senha = "";
$database = "gestaoJogoDB";
$conn = new mysqli($servidor,$username,$senha,$database);
function salvarPerguntas($conn, $perguntas)
{
    foreach ($perguntas as $pergunta) {
        $sql = "SELECT id FROM perguntas WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $pergunta['id']);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $sql = "UPDATE perguntas SET texto = ?, tipo = ?, respostas = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $respostas_serializadas = json_encode($pergunta['respostas']);
            $stmt->bind_param("sssi", $pergunta['texto'], $pergunta['tipo'], $respostas_serializadas, $pergunta['id']);
            $stmt->execute();
        } else {
            $sql = "INSERT INTO perguntas (id, texto, tipo, respostas) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $respostas_serializadas = json_encode($pergunta['respostas']);
            $stmt->bind_param("isss", $pergunta['id'], $pergunta['texto'], $pergunta['tipo'], $respostas_serializadas);
            $stmt->execute();
        }
        $stmt->close();
    }
}
function alterarPergunta($conn, $id, $dados_pergunta)
{
    $sql = "UPDATE perguntas SET texto = ?, tipo = ?, respostas = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $respostas_serializadas = json_encode($dados_pergunta['respostas']);
    $stmt->bind_param("sssi", $dados_pergunta['texto'], $dados_pergunta['tipo'], $respostas_serializadas, $id);
    $stmt->execute();
    $stmt->close();
}
$entidade = $_GET['entidade'] ?? 'perguntas';
$acao = $_GET['acao'] ?? 'listar';
$id = $_GET['id'] ?? null;
$mensagem = '';
$erros = [];

if ($entidade === 'perguntas') {
    $perguntas = carregarPerguntas();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id_form = $_POST['id'] ?? null;
        $texto_pergunta = trim($_POST['texto_pergunta'] ?? '');
        $tipo_pergunta = $_POST['tipo_pergunta'] ?? '';

        if (empty($texto_pergunta)) $erros[] = 'O texto da pergunta é obrigatório!';
        if (empty($tipo_pergunta)) $erros[] = 'O tipo da pergunta é obrigatório';
        $respostas = [];
        if ($tipo_pergunta === 'multipla_escolha') {
            $opcoes = $_POST['opcoes'] ?? [];
            foreach ($opcoes as $opcao_texto) {
                if (trim($opcao_texto) !== '') {
                    $respostas[] = ['id' => chr(97 + count($respostas)), 'texto' => trim($opcao_texto)];
                }
            }
            if (count($respostas) < 5) {
                $erros[] = 'Perguntas de múltipla escolha devem ter ao menos 5 alternativas';
            }
        }
        if (empty($erros)) {
            $dados_pergunta = [
                'texto' => $texto_pergunta,
                'tipo' => $tipo_pergunta,
                'respostas' => $respostas
            ];
        if ($acao === 'criar') {
                $novoId = proximoId($perguntas);
                $perguntas[$novoId] = array_merge(['id' => $novoId], $dados_pergunta);
                $mensagem = 'Pergunta criada com sucesso!';
            } else if ($acao === 'alterar' && isset($perguntas[$id_form])) {
                alterarPergunta($conn,$id_form,$dados_pergunta);
                $mensagem = 'Pergunta alterada com sucesso!';
            }
            salvarPerguntas($perguntas);
            header('Location: ?entidade=perguntas&mensagem=' . urlencode($mensagem));
            exit;
        }
    }
}
