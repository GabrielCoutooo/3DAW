
<?php
define('ARQUIVO_PERGUNTAS', 'perguntas.csv');
define('ARQUIVO_USUARIOS', 'usuarios.csv');

function carregarUsuarios()
{
    if (!file_exists(ARQUIVO_USUARIOS)) {
        return [];
    }
    $usuarios = [];
    $handle = fopen(ARQUIVO_USUARIOS, 'r');
    while (($dados = fgetcsv($handle)) !== FALSE) {
        $usuarios[$dados[0]] = ['id' => $dados[0], 'nome' => $dados[1], 'senha_hash' => $dados[2]];
    }
    fclose($handle);
    return $usuarios;
}
function salvarUsuarios($usuarios)
{
    $handle = fopen(ARQUIVO_USUARIOS, 'w');
    foreach ($usuarios as $u) {
        fputcsv($handle, [$u['id'], $u['nome'], $u['senha_hash']]);
    }
    fclose($handle);
}
function carregarPerguntas()
{
    if (!file_exists(ARQUIVO_PERGUNTAS)) {
        return [];
    }
    $perguntas = [];
    $handle = fopen(ARQUIVO_PERGUNTAS, 'r');
    while (($dados = fgetcsv($handle)) !== FALSE) {
        $respostas = [];
        if ($dados[2] === 'multipla_escolha') {
            if (!empty($dados[3])) $respostas[] = ['id' => 'a', 'texto' => $dados[3]];
            if (!empty($dados[4])) $respostas[] = ['id' => 'b', 'texto' => $dados[4]];
            if (!empty($dados[5])) $respostas[] = ['id' => 'c', 'texto' => $dados[5]];
            if (!empty($dados[6])) $respostas[] = ['id' => 'd', 'texto' => $dados[6]];
            if (!empty($dados[7])) $respostas[] = ['id' => 'e', 'texto' => $dados[7]];
        }
        $perguntas[$dados[0]] = [
            'id' => $dados[0],
            'texto' => $dados[1],
            'tipo' => $dados[2],
            'respostas' => $respostas
        ];
    }
    fclose($handle);
    return $perguntas;
}
function salvarPerguntas($perguntas)
{
    $handle = fopen(ARQUIVO_PERGUNTAS, "w");
    foreach ($perguntas as $pergunta) {
        $linha = [
            $pergunta['id'],
            $pergunta['texto'],
            $pergunta['tipo'],
            $pergunta['respostas'][0]['texto'] ?? '',
            $pergunta['respostas'][1]['texto'] ?? '',
            $pergunta['respostas'][2]['texto'] ?? '',
            $pergunta['respostas'][3]['texto'] ?? '',
            $pergunta['respostas'][4]['texto'] ?? ''
        ];
        fputcsv($handle, $linha);
    }
    fclose($handle);
}

function proximoId($array_dados)
{
    return count($array_dados) > 0 ? max(array_keys($array_dados)) + 1 : 1;
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
                $perguntas[$id_form] = array_merge($perguntas[$id_form], $dados_pergunta);
                $mensagem = 'Pergunta alterada com sucesso!';
            }
            salvarPerguntas($perguntas);
            header('Location: ?entidade=perguntas&mensagem=' . urlencode($mensagem));
            exit;
        }
    }

    if ($acao === 'excluir' && $id && isset($perguntas[$id])) {
        unset($perguntas[$id]);
        salvarPerguntas($perguntas);
        header('Location: ?entidade=perguntas&mensagem=' . urlencode('Perguta excluída com sucesso!'));
        exit;
    }
} elseif ($entidade === 'usuarios') {
    $usuarios = carregarUsuarios();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id_form = $_POST['id'] ?? null;
        $nome = trim($_POST['nome'] ?? '');
        $senha = $_POST['senha'] ?? '';
        if (empty($nome)) $erros[] = 'O nome do usuario é obrigatorio!';
        if ($acao === 'criar' && empty($senha)) {
            $erros[] = 'A senha é obrigatória para criar um novo usuário!';
        }
        if (empty($erros)) {
            $dados_usuario = ['nome' => $nome];
            if (!empty($senha)) {
                $dados_usuario['senha_hash'] = password_hash($senha, PASSWORD_DEFAULT);
            }
            if ($acao === 'criar') {
                $novoId = proximoId($usuarios);
                $usuarios[$novoId] = array_merge(['id' => $novoId], $dados_usuario);
                $mensagem = 'Usuario criado com sucesso!';
            } elseif ($acao === 'alterar' && isset($usuarios[$id_form])) {
                $usuarios[$id_form] = array_merge($usuarios[$id_form], $dados_usuario);
                $mensagem = 'Usuario alterado com sucesso!';
            }
            salvarUsuarios($usuarios);
            header('Location: ?entidade=usuarios&mensagem=' . urlencode($mensagem));
            exit;
        }
    }
    if ($acao === 'excluir' && $id && isset($usuarios[$id])) {
        unset($usuarios[$id]);
        salvarUsuarios($usuarios);
        header('Location: ?entidade=usuarios&mensagem=' . urlencode('Usuário excluído com sucesso!'));
        exit;
    }
}
if (isset($_GET['mensagem'])) {
    $mensagem = htmlspecialchars($_GET['mensagem']);
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Game Corporativo - AV1 3DAW</title>
    <style>
        :root {
            --cor-primaria: #007bff;
            --cor-primaria-hover: #0056b3;
            --cor-sucesso: #28a745;
            --cor-sucesso-hover: #218838;
            --cor-neutra: #6c757d;
            --cor-neutra-hover: #5a6268;
            --cor-perigo: #dc3545;
            --cor-perigo-hover: #c82333;
            --cor-fundo: #f8f9fa;
            --cor-borda: #dee2e6;
            --cor-texto: #212529;
            --cor-sombra: rgba(0, 0, 0, 0.1);
        }


        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: var(--cor-fundo);
            color: var(--cor-texto);
            margin: 0;
            padding: 20px;
            line-height: 1.5;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px var(--cor-sombra);
        }

        h1,
        h2 {
            color: #343a40;
            border-bottom: 1px solid var(--cor-borda);
            padding-bottom: 10px;
            margin-top: 0;
            margin-bottom: 25px;
        }

        a {
            text-decoration: none;
            color: var(--cor-primaria);
            transition: color 0.2s ease-in-out;
        }

        a:hover {
            color: var(--cor-primaria-hover);
        }


        .nav {
            background: #e9ecef;
            padding: 10px;
            margin-bottom: 30px;
            border-radius: 8px;
            text-align: center;
        }

        .nav a {
            font-weight: 500;
            margin: 0 15px;
            padding: 8px 15px;
            color: #495057;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.2s ease-in-out, color 0.2s ease-in-out;
        }

        .nav a:hover {
            background-color: #ced4da;
            color: #000;
        }

        .nav a.active {
            color: #fff;
            background: var(--cor-primaria);
        }


        .botao {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 5px;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: 500;
            font-size: 16px;
            text-align: center;
            text-decoration: none;
            transition: background-color 0.2s ease-in-out, transform 0.1s ease-in-out;
        }

        .botao:hover {
            transform: translateY(-2px);

        }

        .botao.novo {
            background-color: var(--cor-sucesso);
            margin-bottom: 20px;
        }

        .botao.novo:hover {
            background-color: var(--cor-sucesso-hover);
        }

        .botao.salvar {
            background-color: var(--cor-primaria);
        }

        .botao.salvar:hover {
            background-color: var(--cor-primaria-hover);
        }

        .botao.cancelar {
            background-color: var(--cor-neutra);
        }

        .botao.cancelar:hover {
            background-color: var(--cor-neutra-hover);
        }


        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }

        th,
        td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid var(--cor-borda);
        }

        th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #495057;
        }

        tbody tr:hover {
            background-color: #f1f3f5;
        }

        .acoes a {
            margin-right: 10px;
            font-weight: 500;
        }

        .acoes a.excluir {
            color: var(--cor-perigo);
        }

        .acoes a.excluir:hover {
            color: var(--cor-perigo-hover);
        }


        .grupo-form {
            margin-bottom: 20px;
        }

        .grupo-form label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #495057;
        }

        .grupo-form input[type="text"],
        .grupo-form input[type="password"],
        .grupo-form textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--cor-borda);
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
            transition: border-color 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }

        .grupo-form input[type="text"]:focus,
        .grupo-form input[type="password"]:focus,
        .grupo-form textarea:focus {
            outline: none;
            border-color: var(--cor-primaria);
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25);
        }

        .grupo-form input[type="radio"] {
            margin-right: 5px;
        }

        .grupo-form label>input[type="radio"] {
            margin-right: 8px;
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
        <div class="nav">
            <a href="?entidade=perguntas" class="<?= $entidade === 'perguntas' ? 'active' : '' ?>">Gerenciar Perguntas</a>
            <a href="?entidade=usuarios" class="<?= $entidade === 'usuarios' ? 'active' : '' ?>">Gerenciar Usuários</a>
        </div>

        <h1>Gestão de <?= ucfirst($entidade) ?></h1>
        <?php if ($mensagem) : ?>
            <div class="mensagem"><?= $mensagem ?></div>
        <?php endif; ?>

        <?php if (!empty($erros)) : ?>
            <div class="erros">
                <strong>Erros:</strong>
                <ul>
                    <?php foreach ($erros as $erro) : ?>
                        <li><?= $erro ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php
        if ($entidade === 'perguntas') :
            if ($acao === 'criar' || $acao === 'alterar') :
                $pergunta_atual = ($acao === 'alterar' && isset($perguntas[$id])) ? $perguntas[$id] : null;
        ?>
                <h2><?= $acao === 'criar' ? 'Criar Nova Pergunta' : 'Alterar Pergunta' ?></h2>
                <form method="POST" action="?entidade=perguntas&acao=<?= $acao ?>">
                    <input type="hidden" name="id" value="<?= $pergunta_atual['id'] ?? '' ?>">
                    <div class="grupo-form">
                        <label>Texto da Pergunta:</label>
                        <textarea name="texto_pergunta" rows="4" required><?= htmlspecialchars($pergunta_atual['texto'] ?? $_POST['texto_pergunta'] ?? '') ?></textarea>
                    </div>
                    <div class="grupo-form">
                        <label>Tipo:</label>
                        <label>
                            <input type="radio" name="tipo_pergunta" value="multipla_escolha" <?= (($pergunta_atual['tipo'] ?? $_POST['tipo_pergunta'] ?? 'multipla_escolha') === 'multipla_escolha') ? 'checked' : '' ?>> Múltipla Escolha
                        </label>
                        <label>
                            <input type="radio" name="tipo_pergunta" value="texto" <?= (($pergunta_atual['tipo'] ?? $_POST['tipo_pergunta'] ?? '') === 'texto') ? 'checked' : '' ?>> Resposta de Texto
                        </label>
                    </div>
                    <div>
                        <label>Respostas (para Múltipla Escolha):</label>
                        <?php for ($i = 0; $i < 5; $i++) :
                            $resposta_texto = $pergunta_atual['respostas'][$i]['texto'] ?? $_POST['opcoes'][$i] ?? '';
                        ?>
                            <input class="grupo-form" type="text" name="opcoes[]" placeholder="Opção <?= $i + 1 ?>" value="<?= htmlspecialchars($resposta_texto) ?>">
                        <?php endfor; ?>
                    </div>
                    <button type="submit" class="botao salvar">Salvar</button>
                    <a href="?entidade=perguntas" class="botao cancelar">Cancelar</a>
                </form>
            <?php else :
            ?>
                <a href="?entidade=perguntas&acao=criar" class="botao novo">Criar Nova Pergunta</a>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Texto</th>
                            <th>Tipo</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($perguntas as $p) : ?>
                            <tr>
                                <td><?= $p['id'] ?></td>
                                <td><?= htmlspecialchars(substr($p['texto'], 0, 50)) . '...' ?></td>
                                <td><?= $p['tipo'] === 'multipla_escolha' ? 'Múltipla Escolha' : 'Texto' ?></td>
                                <td class="acoes">
                                    <a href="?entidade=perguntas&acao=alterar&id=<?= $p['id'] ?>">Alterar</a>
                                    <a href="?entidade=perguntas&acao=excluir&id=<?= $p['id'] ?>" class="excluir">Excluir</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <?php
        elseif ($entidade === 'usuarios') :
            if ($acao === 'criar' || $acao === 'alterar') :
                $usuario_atual = ($acao === 'alterar' && isset($usuarios[$id])) ? $usuarios[$id] : null;
            ?>
                <h2><?= $acao === 'criar' ? 'Criar Novo Usuário' : 'Alterar Usuário' ?></h2>
                <form method="POST" action="?entidade=usuarios&acao=<?= $acao ?>">
                    <input type="hidden" name="id" value="<?= $usuario_atual['id'] ?? '' ?>">
                    <div class="grupo-form">
                        <label for="nome">Nome:</label>
                        <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($usuario_atual['nome'] ?? $_POST['nome'] ?? '') ?>" required>
                    </div>
                    <div class="grupo-form">
                        <label for="senha">Senha:</label>
                        <input type="password" id="senha" name="senha" placeholder="<?= $acao === 'alterar' ? 'Deixe em branco para não alterar' : '' ?>">
                    </div>
                    <button type="submit" class="botao salvar">Salvar</button>
                    <a href="?entidade=usuarios" class="botao cancelar">Cancelar</a>
                </form>
            <?php else :
            ?>
                <a href="?entidade=usuarios&acao=criar" class="botao novo">Criar Novo Usuário</a>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $u) : ?>
                            <tr>
                                <td><?= $u['id'] ?></td>
                                <td><?= htmlspecialchars($u['nome']) ?></td>
                                <td class="acoes">
                                    <a href="?entidade=usuarios&acao=alterar&id=<?= $u['id'] ?>">Alterar</a>
                                    <a href="?entidade=usuarios&acao=excluir&id=<?= $u['id'] ?>" class="excluir">Excluir</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>

</html>
