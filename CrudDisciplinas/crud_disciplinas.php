<?php

define('ARQUIVO_DISCIPLINAS', 'disciplinas.txt');

function carregarDisciplinas()
{
    if (!file_exists(ARQUIVO_DISCIPLINAS)) {
        return [];
    }
    $disciplinas = [];
    $linhas = file(ARQUIVO_DISCIPLINAS, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    if (isset($linhas[0])) unset($linhas[0]);

    foreach ($linhas as $linha) {
        $dados = explode(";", $linha);
        if (count($dados) === 4) {
            $disciplinas[$dados[0]] = ['id' => $dados[0], 'nome' => $dados[1], 'sigla' => $dados[2], 'carga' => $dados[3]];
        }
    }
    return $disciplinas;
}

function salvarDisciplinas($disciplinas)
{
    $conteudo = "id;nome;sigla;carga\n";
    foreach ($disciplinas as $d) {
        $conteudo .= implode(";", $d) . "\n";
    }
    file_put_contents(ARQUIVO_DISCIPLINAS, $conteudo);
}

function proximoId($disciplinas)
{
    return count($disciplinas) > 0 ? max(array_keys($disciplinas)) + 1 : 1;
}

$acao = $_GET['acao'] ?? 'listar';
$id = $_GET['id'] ?? null;
$mensagem = '';
$erros = [];

$disciplinas = carregarDisciplinas();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_form = $_POST['id'] ?? null;
    $nome = trim($_POST['nome'] ?? '');
    $sigla = trim($_POST['sigla'] ?? '');
    $carga = trim($_POST['carga'] ?? '');

    if (empty($nome)) $erros[] = 'O nome é obrigatório.';
    if (empty($sigla)) $erros[] = 'A sigla é obrigatória.';
    if (empty($carga) || !is_numeric($carga) || $carga <= 0) $erros[] = 'A carga horária deve ser um número positivo.';

    if (empty($erros)) {
        $dados_disciplina = ['nome' => $nome, 'sigla' => $sigla, 'carga' => $carga];

        if ($id_form) {
            $disciplinas[$id_form] = array_merge($disciplinas[$id_form], $dados_disciplina);
            $mensagem = 'Disciplina alterada com sucesso!';
        } else {
            $novoId = proximoId($disciplinas);
            $disciplinas[$novoId] = array_merge(['id' => $novoId], $dados_disciplina);
            $mensagem = 'Disciplina salva com sucesso!';
        }

        salvarDisciplinas($disciplinas);
        header('Location: ?mensagem=' . urlencode($mensagem));
        exit;
    }
}

if ($acao === 'excluir' && $id && isset($disciplinas[$id])) {
    unset($disciplinas[$id]);
    salvarDisciplinas($disciplinas);
    header('Location: ?mensagem=' . urlencode('Disciplina excluída com sucesso!'));
    exit;
}

if (isset($_GET['mensagem'])) {
    $mensagem = htmlspecialchars($_GET['mensagem']);
}

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>CRUD de Disciplinas</title>
    <style>
        body {
            font-family: system-ui, sans-serif;
            background-color: #f4f7f9;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h1,
        h2 {
            color: #2c3e50;
            border-bottom: 1px solid #ecf0f1;
            padding-bottom: 10px;
        }

        hr {
            border: none;
            border-top: 1px solid #ecf0f1;
            margin: 30px 0;
        }

        .botao {
            display: inline-block;
            padding: 10px 18px;
            border-radius: 5px;
            color: #fff;
            border: none;
            cursor: pointer;
            font-weight: 500;
            text-decoration: none;
            transition: background-color 0.2s;
        }

        .botao.salvar {
            background-color: #3498db;
        }

        .botao.salvar:hover {
            background-color: #2980b9;
        }

        .botao.cancelar {
            background-color: #95a5a6;
        }

        .botao.cancelar:hover {
            background-color: #7f8c8d;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        .acoes a {
            margin-right: 8px;
            color: #3498db;
            font-weight: 500;
            text-decoration: none;
        }

        .acoes a.excluir {
            color: #e74c3c;
        }

        .grupo-form {
            margin-bottom: 20px;
        }

        .grupo-form label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .grupo-form input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .mensagem,
        .erros {
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            border: 1px solid;
        }

        .mensagem {
            background-color: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }

        .erros {
            background-color: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
        }
    </style>
</head>

<body>
    <div class="container">

        <?php
        $disciplina_atual = null;
        if ($acao === 'alterar' && isset($disciplinas[$id])) {
            $titulo_form = "Alterar Disciplina";
            $disciplina_atual = $disciplinas[$id];
        } else {
            $titulo_form = "Cadastro de Disciplinas";
        }
        ?>

        <h2><?= $titulo_form ?></h2>

        <?php if ($mensagem): ?><div class="mensagem"><?= $mensagem ?></div><?php endif; ?>
        <?php if (!empty($erros)): ?>
            <div class="erros"><strong>Erros:</strong>
                <ul><?php foreach ($erros as $erro): ?><li><?= $erro ?></li><?php endforeach; ?></ul>
            </div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="id" value="<?= htmlspecialchars($disciplina_atual['id'] ?? '') ?>">
            <div class="grupo-form">
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($disciplina_atual['nome'] ?? '') ?>" required>
            </div>
            <div class="grupo-form">
                <label for="sigla">Sigla:</label>
                <input type="text" id="sigla" name="sigla" value="<?= htmlspecialchars($disciplina_atual['sigla'] ?? '') ?>" required>
            </div>
            <div class="grupo-form">
                <label for="carga">Carga horária:</label>
                <input type="number" id="carga" name="carga" value="<?= htmlspecialchars($disciplina_atual['carga'] ?? '') ?>" required min="1">
            </div>
            <button type="submit" class="botao salvar">Salvar</button>
            <?php if ($acao === 'alterar'): ?>
                <a href="?" class="botao cancelar">Cancelar Alteração</a>
            <?php endif; ?>
        </form>

        <hr>
        <h2>Lista de Disciplinas</h2>

        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Sigla</th>
                    <th>Carga Horária</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($disciplinas)): ?>
                    <tr>
                        <td colspan="4" style="text-align:center;">Nenhuma disciplina cadastrada.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($disciplinas as $d): ?>
                        <tr>
                            <td><?= htmlspecialchars($d['nome']) ?></td>
                            <td><?= htmlspecialchars($d['sigla']) ?></td>
                            <td><?= htmlspecialchars($d['carga']) ?>h</td>
                            <td class="acoes">
                                <a href="?acao=alterar&id=<?= $d['id'] ?>">Alterar</a>
                                <a href="?acao=excluir&id=<?= $d['id'] ?>" class="excluir">Excluir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>

</html>
