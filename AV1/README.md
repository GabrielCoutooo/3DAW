FAETERJ-Rio
Desenvolvimento de Aplicações Web - 3DAWAV1 3DAW 2025-2

O Sr. Water Falls precisa de um sistema de jogo corporativo, para treinar seus gestores em situações difíceis. O jogo deverá gerenciar situações de perguntas e respostas (decisões) encadeadas.
O game é composto por vários desafios e cada desafio tem um objetivo específico, como por exemplo, gerenciar o andamento de um projeto, resolver um problema administrativo, contratar um novo funcionário, conceder um empréstimo e outros.
Neste primeiro momento será desenvolvido somente o cadastro Usuários, Perguntas e Respostas.
Criar as funcionalidades de Criar Perguntas e respostas de multipla escolha, Criar Perguntas e respostas de texto,  alterar Perguntas e suas respostas de multipla escolha, listar todas Perguntas, listar uma Pergunta e excluir Pergunta e respostas.
Inicialmente usaremos arquivos texto(txt) para salvar os usuários.
As funcionalidades de Perguntas e respostas devem estar disponíveis por tela.
O código deverá ser em PHP.
Então deverá ser criado:
1. Criar Perguntas e respostas de multipla escolha.
2.Criar Perguntas e respostas de texto.
3. Alterar Perguntas e suas respostas de multipla escolha
4. Alterar Perguntas com respostas de texto
5. Listar Perguntas e repostas.
6. Listar uma Pergunta.
7. Excluir Pergunta e respostas
8. CRUD de Usuarios

### Notas sobre as Decisões do Projeto

Para a realização deste trabalho, foram tomadas algumas decisões de arquitetura e estilo, visando a clareza e o alinhamento com os objetivos didáticos da disciplina:

* **Estrutura em Arquivo Único:** Todo o código (PHP, CSS e HTML) foi centralizado em um **único arquivo** para simplificar a visualização do fluxo de dados e da lógica do CRUD em um só local.

* **Persistência em Arquivos `.csv`:** Seguindo o modelo proposto em aula e os requisitos da avaliação, os dados são armazenados em arquivos de texto no formato **CSV**.

* **Sintaxe Alternativa do PHP:** Foi utilizada a sintaxe de controle com **`endif`** e **`endforeach`** na parte do HTML para melhorar a legibilidade e a identificação dos blocos de código PHP dentro das TAGS.

* **Segurança:** No CRUD de usuários, foi implementada a função **`password_hash()`** para garantir que as senhas sejam armazenadas de forma segura e criptografada, seguindo as melhores práticas do mercado.

