let alunoEmEdicao = null;

document.addEventListener('DOMContentLoaded', () => {
    carregarAlunos();
    setupEventListeners();
});

function setupEventListeners() {
    const form = document.getElementById('form-aluno');
    const btnCancelar = document.getElementById('btn-cancelar');

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        salvarAluno();
    });

    btnCancelar.addEventListener('click', cancelarEdicao);
}

function carregarAlunos() {
    const loading = document.getElementById('loading');
    const listVazia = document.getElementById('lista-vazia');
    const tabela = document.getElementById('tabela-alunos');

    loading.style.display = 'block';
    listVazia.style.display = 'none';
    tabela.style.display = 'none';

    fetch('alunos.php?acao=listar')
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro ao buscar alunos');
            }
            return response.json();
        })
        .then(alunos => {
            loading.style.display = 'none';

            if (alunos.length === 0) {
                listVazia.style.display = 'block';
            } else {
                preencherTabela(alunos);
                tabela.style.display = 'table';
            }
        })
        .catch(erro => {
            loading.style.display = 'none';
            mostrarMensagem('Erro ao carregar alunos: ' + erro.message, 'erro');
            console.error('Erro:', erro);
        });
}

function preencherTabela(alunos) {
    const tbody = document.getElementById('tbody-alunos');
    tbody.innerHTML = '';

    alunos.forEach(aluno => {
        const linha = document.createElement('tr');
        linha.innerHTML = `
            <td>${aluno.id}</td>
            <td>${escapeHtml(aluno.nome)}</td>
            <td>${escapeHtml(aluno.matricula)}</td>
            <td>${escapeHtml(aluno.email)}</td>
            <td>
                <div class="acoes">
                    <button class="btn btn-edit" onclick="editarAluno(${aluno.id}, '${escapeJs(aluno.nome)}', '${escapeJs(aluno.matricula)}', '${escapeJs(aluno.email)}')">Editar</button>
                    <button class="btn btn-delete" onclick="deletarAlunoConfirm(${aluno.id})">Deletar</button>
                </div>
            </td>
        `;
        tbody.appendChild(linha);
    });
}

function salvarAluno() {
    const nome = document.getElementById('nome').value.trim();
    const matricula = document.getElementById('matricula').value.trim();
    const email = document.getElementById('email').value.trim();

    if (!nome || !matricula || !email) {
        mostrarMensagem('Por favor, preencha todos os campos', 'erro');
        return;
    }

    const dados = {
        nome: nome,
        matricula: matricula,
        email: email
    };

    const url = alunoEmEdicao 
        ? `alunos.php?acao=atualizar`
        : `alunos.php?acao=criar`;

    if (alunoEmEdicao) {
        dados.id = alunoEmEdicao;
    }

    const opcoes = {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(dados)
    };

    fetch(url, opcoes)
        .then(response => {
            if (response.status === 201 || response.status === 200) {
                return response.json();
            } else {
                return response.json().then(data => {
                    throw new Error(data.erro || 'Erro ao salvar aluno');
                });
            }
        })
        .then(data => {
            mostrarMensagem(data.mensagem, 'sucesso');
            limparFormulario();
            cancelarEdicao();
            carregarAlunos();
        })
        .catch(erro => {
            mostrarMensagem('Erro: ' + erro.message, 'erro');
            console.error('Erro:', erro);
        });
}

function editarAluno(id, nome, matricula, email) {
    alunoEmEdicao = id;
    document.getElementById('nome').value = nome;
    document.getElementById('matricula').value = matricula;
    document.getElementById('email').value = email;
    document.getElementById('form-title').textContent = 'Editar Aluno';
    document.getElementById('btn-submit').textContent = 'Atualizar Aluno';
    document.getElementById('btn-cancelar').style.display = 'inline-block';
    document.getElementById('form-aluno').scrollIntoView({ behavior: 'smooth' });
}

function cancelarEdicao() {
    alunoEmEdicao = null;
    limparFormulario();
    document.getElementById('form-title').textContent = 'Adicionar Novo Aluno';
    document.getElementById('btn-submit').textContent = 'Adicionar Aluno';
    document.getElementById('btn-cancelar').style.display = 'none';
}

function limparFormulario() {
    document.getElementById('form-aluno').reset();
    document.getElementById('mensagem').innerHTML = '';
    document.getElementById('mensagem').className = 'mensagem';
}

function deletarAlunoConfirm(id) {
    if (confirm('Tem certeza que deseja deletar este aluno?')) {
        deletarAluno(id);
    }
}

function deletarAluno(id) {
    fetch(`alunos.php?acao=deletar&id=${id}`, {
        method: 'DELETE'
    })
        .then(response => {
            if (response.ok) {
                return response.json();
            } else {
                return response.json().then(data => {
                    throw new Error(data.erro || 'Erro ao deletar aluno');
                });
            }
        })
        .then(data => {
            mostrarMensagem(data.mensagem, 'sucesso');
            carregarAlunos();
        })
        .catch(erro => {
            mostrarMensagem('Erro: ' + erro.message, 'erro');
            console.error('Erro:', erro);
        });
}

function mostrarMensagem(texto, tipo) {
    const mensagemDiv = document.getElementById('mensagem');
    mensagemDiv.textContent = texto;
    mensagemDiv.className = `mensagem ${tipo}`;

    setTimeout(() => {
        mensagemDiv.className = 'mensagem';
        mensagemDiv.innerHTML = '';
    }, 5000);
}

function escapeHtml(texto) {
    const div = document.createElement('div');
    div.textContent = texto;
    return div.innerHTML;
}

function escapeJs(texto) {
    return texto.replace(/'/g, "\\'").replace(/"/g, '\\"').replace(/\n/g, '\\n').replace(/\r/g, '\\r');
}
