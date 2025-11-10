const conteudo = document.getElementById('conteudo');
const titulo = document.getElementById('titulo');
const btnPerguntas = document.getElementById('btnPerguntas');
const btnUsuarios = document.getElementById('btnUsuarios');

let entidadeAtual = 'perguntas';

function fazerRequisicao(url, metodo = 'GET', dados = null, sucesso, erro) {
    const xhr = new XMLHttpRequest();
    xhr.open(metodo, url, true);
    
    xhr.onload = function() {
        if (xhr.status >= 200 && xhr.status < 300) {
            try {
                const resposta = JSON.parse(xhr.responseText);
                if (sucesso) sucesso(resposta);
            } catch (e) {
                if (erro) erro('Erro ao processar resposta do servidor');
            }
        } else {
            try {
                const erroObj = JSON.parse(xhr.responseText);
                if (erro) erro(erroObj.erros?.join('\n') || 'Erro na requisição');
            } catch (e) {
                if (erro) erro('Erro na requisição');
            }
        }
    };

    xhr.onerror = function() {
        if (erro) erro('Erro de rede');
    };

    if (dados instanceof FormData) {
        xhr.send(dados);
    } else if (dados) {
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.send(JSON.stringify(dados));
    } else {
        xhr.send();
    }
}

btnPerguntas.onclick = () => carregar('perguntas');
btnUsuarios.onclick = () => carregar('usuarios');

function carregar(tipo) {
    entidadeAtual = tipo;
    titulo.textContent = tipo === 'usuarios' ? 'Gestão de Usuários' : 'Gestão de Perguntas';
    btnPerguntas.classList.toggle('ativo', tipo === 'perguntas');
    btnUsuarios.classList.toggle('ativo', tipo === 'usuarios');

    fazerRequisicao(
        `../Backend/index.php?entidade=${tipo}&acao=listar`,
        'GET',
        null,
        function(dados) {
            mostrarConteudo(gerarTabelaEBotoes(tipo, dados));
        },
        function(erro) {
            mostrarErro('Erro ao carregar dados: ' + erro);
        }
    );
}

function gerarTabelaEBotoes(tipo, dados) {
    let html = `<button class="botao novo" onclick="mostrarFormulario()">Criar Novo ${tipo === 'usuarios' ? 'Usuário' : 'Pergunta'}</button>`;
    html += gerarTabela(tipo, dados);
    return html;
}

function gerarTabela(tipo, dados) {
    let html = `<table><thead><tr>`;
    if (tipo === 'usuarios') {
        html += `<th>ID</th><th>Nome</th><th>Ações</th></tr></thead><tbody>`;
        Object.values(dados).forEach(u => {
            html += `<tr>
                <td>${u.id}</td>
                <td>${u.nome}</td>
                <td>
                    <button class="botao" onclick="editarItem(${u.id})">Editar</button>
                    <button class="botao excluir" onclick="excluirItem(${u.id})">Excluir</button>
                </td>
            </tr>`;
        });
    } else {
        html += `<th>ID</th><th>Pergunta</th><th>Tipo</th><th>Ações</th></tr></thead><tbody>`;
        Object.values(dados).forEach(p => {
            html += `<tr>
                <td>${p.id}</td>
                <td>${p.perguntas}</td>
                <td>${p.tipo === 'multipla_escolha' ? 'Múltipla Escolha' : 'Texto'}</td>
                <td>
                    <button class="botao" onclick="editarItem(${p.id})">Editar</button>
                    <button class="botao excluir" onclick="excluirItem(${p.id})">Excluir</button>
                </td>
            </tr>`;
        });
    }
    html += `</tbody></table>`;
    return html;
}

function mostrarFormulario(item = null) {
    const isEdicao = item !== null;
    let html = `
        <h2>${isEdicao ? 'Editar' : 'Criar'} ${entidadeAtual === 'usuarios' ? 'Usuário' : 'Pergunta'}</h2>
        <form onsubmit="salvarItem(event)">
            ${item ? '<input type="hidden" name="id" value="' + item.id + '">' : ''}`;

    if (entidadeAtual === 'usuarios') {
        html += `
            <div class="grupo-form">
                <label>Nome:</label>
                <input type="text" name="nome" value="${item?.nome || ''}" required>
            </div>
            <div class="grupo-form">
                <label>Senha:</label>
                <input type="password" name="senha" ${!isEdicao ? 'required' : ''} placeholder="${isEdicao ? 'Deixe em branco para não alterar' : ''}">
            </div>`;
    } else {
        html += `
            <div class="grupo-form">
                <label>Texto da Pergunta:</label>
                <textarea name="texto_pergunta" rows="4" required>${item?.perguntas || ''}</textarea>
            </div>
            <div class="grupo-form">
                <label>
                    <input type="radio" name="tipo_pergunta" value="multipla_escolha" ${(!item || item.tipo === 'multipla_escolha') ? 'checked' : ''}>
                    Múltipla Escolha
                </label>
                <label>
                    <input type="radio" name="tipo_pergunta" value="texto" ${(item?.tipo === 'texto') ? 'checked' : ''}>
                    Resposta de Texto
                </label>
            </div>
            <div id="opcoesMultipla" class="grupo-form">
                <label>Respostas (para Múltipla Escolha):</label>
                ${Array(5).fill().map((_, i) => `
                    <input type="text" name="opcoes[]" placeholder="Opção ${i + 1}" 
                           value="${item?.respostas?.[i]?.respostas || ''}" class="grupo-form">
                `).join('')}
            </div>`;
    }

    html += `
            <button type="submit" class="botao salvar">Salvar</button>
            <button type="button" class="botao cancelar" onclick="carregar('${entidadeAtual}')">Cancelar</button>
        </form>`;

    mostrarConteudo(html);
}

function salvarItem(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    const id = formData.get('id');
    const acao = id ? 'alterar' : 'criar';

    fazerRequisicao(
        `../Backend/index.php?entidade=${entidadeAtual}&acao=${acao}${id ? '&id=' + id : ''}`,
        'POST',
        formData,
        function(data) {
            mostrarMensagem(data.mensagem);
            carregar(entidadeAtual);
        },
        function(erro) {
            mostrarErro(erro);
        }
    );
}

function excluirItem(id) {
    if (!confirm('Tem certeza que deseja excluir este item?')) return;

    fazerRequisicao(
        `../Backend/index.php?entidade=${entidadeAtual}&acao=excluir&id=${id}`,
        'GET',
        null,
        function(data) {
            mostrarMensagem(data.mensagem);
            carregar(entidadeAtual);
        },
        function(erro) {
            mostrarErro(erro);
        }
    );
}

function editarItem(id) {
    fazerRequisicao(
        `../Backend/index.php?entidade=${entidadeAtual}&acao=listar`,
        'GET',
        null,
        function(dados) {
            const item = dados[id];
            if (!item) {
                mostrarErro('Item não encontrado');
                return;
            }
            mostrarFormulario(item);
        },
        function(erro) {
            mostrarErro(erro);
        }
    );
}

function mostrarConteudo(html) {
    conteudo.innerHTML = html;
}

function mostrarMensagem(texto) {
    const div = document.createElement('div');
    div.className = 'mensagem';
    div.textContent = texto;
    conteudo.insertAdjacentElement('afterbegin', div);
    setTimeout(() => div.remove(), 3000);
}

function mostrarErro(texto) {
    const div = document.createElement('div');
    div.className = 'erros';
    div.textContent = texto;
    conteudo.insertAdjacentElement('afterbegin', div);
    setTimeout(() => div.remove(), 3000);
}

carregar('perguntas');