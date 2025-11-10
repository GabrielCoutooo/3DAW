    document.getElementById('buscarForm').addEventListener('submit', async (e) => {
      e.preventDefault();
      const codigo = document.getElementById('codigo').value;

      const response = await fetch('backend.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ acao: 'buscar', codigo })
      });

      const data = await response.json();

      if (data.sucesso) {
        document.getElementById('codigoAlterar').value = codigo;
        document.getElementById('pergunta').value = data.pergunta;
        document.getElementById('formAlterar').style.display = 'block';
        document.getElementById('mensagem').innerText = '';
      } else {
        document.getElementById('formAlterar').style.display = 'none';
        document.getElementById('mensagem').innerText = data.mensagem;
      }
    });

    document.getElementById('alterarForm').addEventListener('submit', async (e) => {
      e.preventDefault();
      const codigo = document.getElementById('codigoAlterar').value;
      const pergunta = document.getElementById('pergunta').value;

      const response = await fetch('backend.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ acao: 'salvar', codigo, pergunta })
      });

      const data = await response.json();
      document.getElementById('mensagem').innerText = data.mensagem;
    });
