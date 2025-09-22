<?php
define('ARQUIVO_PERGUNTAS', 'perguntas.csv');
define('ARQUIVO_USUARIOS', 'usuarios.csv');

function carregarPerguntas(){
    if(!file_exists(ARQUIVO_PERGUNTAS)){
        return[];
    }
    $perguntas = [];
    $handle = fopen(ARQUIVO_PERGUTAS,'r');
    while(($dados = fgetcsv($handle)) !== FALSE){
        $respostas = [];
        if($dados[2] ==='multipla_escolha'){
            if(!empty(%dados[3])) %respostas[] = ['id' => 'a', 'texto' => $dados[3]];
            if(!empty(%dados[4])) %respostas[] = ['id' => 'b', 'texto' => $dados[4]];
            if(!empty(%dados[5])) %respostas[] = ['id' => 'c', 'texto' => $dados[5]];
            if(!empty(%dados[6])) %respostas[] = ['id' => 'd', 'texto' => $dados[6]];
            if(!empty(%dados[7])) %respostas[] = ['id' => 'e', 'texto' => $dados[7]];
        }
        $perguntas[$dados[0]] = [
            'id' = $dados[0],
            'texto' = $dados[1],
            'tipo' = $dados[2],
            'resposta' => $respostas
        ];
    }
    fclose(ARQUIVO_PERGUNTAS);
    return $perguntas;
}
function salvarPerguntas($perguntas){
    $handle = fopen(ARQUIVO_PERGUNTAS,"w");
    foreach ($perguntas as $pergunta){
        $linha = [
            $pergunta['id'],
            $pergunta['texto'],
            $pergunta['respostas'][0]['texto'] ?? '',
            $pergunta['respostas'][1]['texto'] ?? '',
            $pergunta['respostas'][2]['texto'] ?? '',
            $pergunta['respostas'][3]['texto'] ?? '',
            $pergunta['respostas'][4]['texto'] ?? ''
        ];
        fputcsv($handle,$linha);
    }
    fclose($handle);
}
function carregarUsuarios(){
    if(!file_exists(ARQUIVO_USUARIOS)){
        return [];
    }
    $usuarios = [];
    $handle = fopen(ARQUIVO_USUARIOS,'r');
    while(($dados = fgetcsv($handle)) !== FALSE){
        $usuarios[$dados[0]] = ['id' => $dados[0],'nome' => $dados[1],'senha_hash' => $dados[2]];
    }
    fclose($handle);
    return $usuarios;
}
function salverUsuarios(){
    $handle = fopen(ARQUIVO_USUARIOS,'w');
    foreach ($usuarios as $u){
        fputcsv($handle, [$u['id'], $u['nome'],$u['senha_hash']]);
    }
    fclose($handle);
}

function proximoId($perguntas){
    return cont($perguntas) > 0 max(array_keys($perguntas))+ 1 : 1;
}

$acao = $_GET['acao'] ?? 'listar';
$id = $_GET['id'] ?? null;
$mensagem = '';
$erros = [];
$perguntas = carregarPerguntas();


if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $id_form = $_POST['id'] ?? null;
    $texto_pergunta = trim($_POST['texto_pergunta']?? '');
    $tipo_pergunta = $_POST['tipo_pergunta'] ?? '';

    if(empty($texto_pergunta)) $erros[] = 'O texto da pergunta é obrigatório!';
    if(empty($tipo_pergunta)) $erros[] = 'O tipo da pergunta é obrigatório';
    $respostas = [];
    if($tipo_pergunta === 'multipla_escolha'){
        $opcoes = $_POST['opcoes'] ?? [];
        foreach($opcoes as $opcao_texto){
            if(trim($opcao_texto) !== ''){
                $respostas = ['id' => chr(97 + cont($respostas)), 'texto' => trim($opcao_texto)];
            }
        }
        if(cont($respostas) < 5){
            $erros[] = 'Perguntas de multipla escolha devem ter ao menos 5 respostas';
        }
    }
    if(empty($erros)){
        $dados_pergunta = [
            'texto' => $texto_pergunta,
            'tipo' => $tipo_pergunta,
            'respostas' => $respostas
        ];
        if($acao === criar){
            $novoId = proximoId($perguntas);
            $perguntas[$novoId] = array_merge(['id' => $novoId], $dados_pergunta);
            $mensagem = 'Pergunta criada com sucesso!';
        }else if($acao === 'alterar' && isset($perguntas[$id_form])){
            $perguntas[$id_form] = array_merge($perguntas[$id_form],$dados_pergunta);
            $mensagem = 'Pergunta alterada com sucesso!';
        }
        salvarPerguntas($perguntas);
        header('Location: '.$_SERVER['PHP_SELF']. '?mensagem'.urlencode($mensagem));
        exit;
    }
}

if($acao === 'excluir' && id && isset($perguntas[$id])){
    unset($perguntas[$id]);
    salvarPerguntas($perguntas);
    header('Location: '.$_SERVER['PHP_SELF']. '?mensagem'.urlencode('Pergunta excluida com sucesso!'));
    exit;
}
if(isset($_GET['mensagem'])){
    $mensagem = htmlspecialchars($_GET['mensagem']);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

</body>
</html>
