<?php
define('ARQUIVO_PERGUNTAS', 'perguntas.csv');
define('ARQUIVO_USUARIOS', 'usuarios.csv');

function carregarCSV($arquivo) {
  if (!file_exists($arquivo)) return [];
  $dados = [];
  $h = fopen($arquivo, 'r');
  while (($l = fgetcsv($h)) !== FALSE) $dados[] = $l;
  fclose($h);
  return $dados;
}

function salvarCSV($arquivo, $dados) {
  $h = fopen($arquivo, 'w');
  foreach ($dados as $linha) fputcsv($h, $linha);
  fclose($h);
}

function resposta($dados) {
  header('Content-Type: application/json');
  echo json_encode($dados);
  exit;
}

$entidade = $_REQUEST['entidade'] ?? 'perguntas';
$acao = $_REQUEST['acao'] ?? 'listar';
$id = $_REQUEST['id'] ?? null;

if ($entidade === 'perguntas') {
  $perguntas = carregarCSV(ARQUIVO_PERGUNTAS);

  if ($acao === 'listar') {
    $registros = [];
    foreach ($perguntas as $p) {
      $registros[] = [
        'id' => $p[0],
        'texto' => $p[1],
        'tipo' => $p[2]
      ];
    }
    resposta(['sucesso'=>true, 'colunas'=>['id','texto','tipo'], 'registros'=>$registros]);
  }

  if ($acao === 'criar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $texto = trim($_POST['texto_pergunta'] ?? '');
    $tipo = $_POST['tipo_pergunta'] ?? '';
    $opcoes = $_POST['opcoes'] ?? [];
    $erros = [];

    if (empty($texto)) $erros[] = "Texto obrigatório";
    if ($tipo === 'multipla_escolha' && count(array_filter($opcoes)) < 5)
      $erros[] = "Mínimo 5 opções";

    if (!empty($erros)) resposta(['sucesso'=>false,'erros'=>$erros]);

    $novoId = count($perguntas) > 0 ? max(array_column($perguntas, 0)) + 1 : 1;
    $linha = [$novoId, $texto, $tipo];
    foreach (range(0,4) as $i) $linha[] = $opcoes[$i] ?? '';
    $perguntas[] = $linha;
    salvarCSV(ARQUIVO_PERGUNTAS, $perguntas);
    resposta(['sucesso'=>true, 'mensagem'=>'Pergunta salva com sucesso']);
  }

  if ($acao === 'excluir' && $id) {
    $novaLista = array_filter($perguntas, fn($p) => $p[0] != $id);
    salvarCSV(ARQUIVO_PERGUNTAS, $novaLista);
    resposta(['sucesso'=>true,'mensagem'=>'Pergunta excluída']);
  }
}

if ($entidade === 'usuarios') {
  $usuarios = carregarCSV(ARQUIVO_USUARIOS);

  if ($acao === 'listar') {
    $registros = [];
    foreach ($usuarios as $u) {
      $registros[] = ['id'=>$u[0], 'nome'=>$u[1]];
    }
    resposta(['sucesso'=>true, 'colunas'=>['id','nome'], 'registros'=>$registros]);
  }

  if ($acao === 'criar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $senha = $_POST['senha'] ?? '';
    if (empty($nome) || empty($senha))
      resposta(['sucesso'=>false, 'erros'=>['Nome e senha obrigatórios']]);
    $novoId = count($usuarios) > 0 ? max(array_column($usuarios, 0)) + 1 : 1;
    $usuarios[] = [$novoId, $nome, password_hash($senha, PASSWORD_DEFAULT)];
    salvarCSV(ARQUIVO_USUARIOS, $usuarios);
    resposta(['sucesso'=>true, 'mensagem'=>'Usuário criado']);
  }

  if ($acao === 'excluir' && $id) {
    $nova = array_filter($usuarios, fn($u) => $u[0] != $id);
    salvarCSV(ARQUIVO_USUARIOS, $nova);
    resposta(['sucesso'=>true, 'mensagem'=>'Usuário excluído']);
  }
}