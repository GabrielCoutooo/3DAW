<?php
function carregarUsuario($conn)
{
    $usuarios = [];
    $sql = "SELECT * FROM usuarios";
    $resultado = $conn->query($sql);
    if($resultado ->num_rows > 0){
        while($dados = $resultado->fetch_assoc()){
            $usuarios[$dados['id']] = [
                'id' => $dados['id'],
                'nome' => $dados['nome'],
                'senha_hash' =>$dados['senha_hash']
            ];
        }
    }
    return $usuarios;
}
function criarUsuario($conn,$nome,$senha_hash)
{
        $sql = "INSERT INTO usuarios(nome,senha_hash) VALUES (?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $nome,$senha_hash);
        $stmt->execute();
        $stmt->close();
}
function alterarUsuario($conn,$id,$nome,$senha_hash=null){
    if($senha_hash){
        $sql = "UPDATE usuarios SET nome=?,senha_hash=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi",$nome,$senha_hash,$id);
    }else{
        $sql = "UPDATE usuarios SET nome=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si",$nome,$id);
    }
    $stmt->execute();
    $stmt->close();
}
function excluirUsuario($conn,$id){
    $sql = "DELETE FROM usuarios where id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i",$id);
    $stmt->execute();
    $stmt->close();
}
function criarPergunta($conn,$pergunta,$tipo,$respostas)
{
    $respostas_json = json_encode($respostas,JSON_UNESCAPED_UNICODE);
    $sql = "INSERT INTO perguntas (perguntas,tipo,respostas) VALUES (?,?,?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss",$pergunta,$tipo,$respostas_json);
    $stmt->execute();
    $stmt->close();
}
function carregarPergunta($conn)
{
    $perguntas = [];
    $sql = "SELECT * FROM perguntas";
    $resultado = $conn->query($sql);
    if($resultado->num_rows >0) {
        while($dados = $resultado->fetch_assoc()){
            $perguntas[$dados['id']] = [
                'id' => $dados['id'],
                'perguntas' => $dados['perguntas'],
                'tipo' => $dados['tipo'],
                'respostas' => json_decode($dados['respostas'], true) ?? []
            ];
        }
    }
    return $perguntas;
}
function alterarPergunta($conn, $id,$pergunta,$tipo,$respostas)
{
    $respostas_json = json_encode($respostas,JSON_UNESCAPED_UNICODE);
    $sql = "UPDATE perguntas SET perguntas = ?, tipo = ?, respostas = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi",$pergunta,$tipo,$respostas_json,$id );
    $stmt->execute();
    $stmt->close();
}

function excluirPergunta($conn,$id){
    $sql = "DELETE FROM perguntas WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i",$id);
    $stmt->execute();
    $stmt->close();
}
?>