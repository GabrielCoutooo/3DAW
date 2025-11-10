let entidadeAtual = "perguntas";

function mostrarMensagem(texto, tipo = "sucesso") {
  const msg = document.getElementById("mensagem");
  if (!msg) return;
  const textoFinal = Array.isArray(texto) ? texto.join('; ') : texto;
  msg.className = 'mensagem ' + (tipo || 'sucesso');
  msg.textContent = textoFinal;
  setTimeout(() => { msg.textContent = ""; msg.className = ""; }, 4000);
}

function getJSON(url, callback) {
  const xhr = new XMLHttpRequest();
  xhr.open("GET", url, true);
  xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
  xhr.onreadystatechange = () => {
    if (xhr.readyState === 4) {
      if (xhr.status === 200) callback(JSON.parse(xhr.responseText));
      else mostrarMensagem("Erro na comunicação", "erro");
    }
  };
  xhr.send();
}

function postJSON(url, dados, callback) {
  const xhr = new XMLHttpRequest();
  xhr.open("POST", url, true);
  xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
  xhr.onreadystatechange = () => {
    if (xhr.readyState === 4) {
      if (xhr.status === 200) callback(JSON.parse(xhr.responseText));
      else mostrarMensagem("Erro no envio", "erro");
    }
  };
  xhr.send(dados);
}

function listar(entidade) {
  entidadeAtual = entidade;
  getJSON(`servidor.php?entidade=${entidade}&acao=listar&modo_assincrono=1`, (dados) => {
    if (!dados.sucesso) {
      mostrarMensagem("Erro ao listar", "erro");
      return;
    }
    const area = document.getElementById("area-lista");
    let html = `<table><tr>`;
    for (let campo of dados.colunas) html += `<th>${campo}</th>`;
    html += `<th>Ações</th></tr>`;
    for (let item of dados.registros) {
      html += "<tr>";
      for (let campo of dados.colunas)
        html += `<td>${item[campo]}</td>`;
      html += `<td><button class="excluir" onclick="excluir(${item.id})">Excluir</button></td></tr>`;
    }
    html += "</table>";
    area.innerHTML = html;
    prepararFormulario();
  });
}

function prepararFormulario() {
  const campos = document.getElementById("campos");
  campos.innerHTML = "";

  if (entidadeAtual === "perguntas") {
    campos.innerHTML = `
      <label>Texto da Pergunta:</label><br>
      <input name="texto_pergunta" required><br>
      <label>Tipo:</label><br>
      <select name="tipo_pergunta">
        <option value="texto">Texto</option>
        <option value="multipla_escolha">Múltipla Escolha</option>
      </select><br>
      <label>Opções (5 mínimas):</label><br>
      ${Array.from({length:5}).map((_,i)=>`<input name="opcoes[]" placeholder="Opção ${i+1}"><br>`).join("")}
    `;
  } else if (entidadeAtual === "usuarios") {
    campos.innerHTML = `
      <label>Nome:</label><br>
      <input name="nome" required><br>
      <label>Senha:</label><br>
      <input type="password" name="senha" required><br>
    `;
  }
}

function enviarFormulario(event) {
  event.preventDefault();
  const form = document.getElementById("formulario");
  const dados = new FormData(form);
  dados.append("entidade", entidadeAtual);
  dados.append("acao", "criar");
  dados.append("modo_assincrono", 1);

  postJSON("servidor.php", dados, (resp) => {
    if (resp.sucesso) {
      mostrarMensagem(resp.mensagem);
      listar(entidadeAtual);
      form.reset();
    } else {
      mostrarMensagem(resp.erros.join("; "), "erro");
    }
  });
}

function excluir(id) {
  if (!confirm("Deseja realmente excluir?")) return;
  getJSON(`servidor.php?entidade=${entidadeAtual}&acao=excluir&id=${id}&modo_assincrono=1`, (resp) => {
    if (resp.sucesso) {
      mostrarMensagem(resp.mensagem);
      listar(entidadeAtual);
    } else mostrarMensagem("Erro ao excluir", "erro");
  });
}

window.onload = () => listar("perguntas");