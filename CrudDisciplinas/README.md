# Sistema de Gestão de Disciplinas

Este projeto é uma aplicação web simples para o gerenciamento de disciplinas acadêmicas, desenvolvida inteiramente em PHP. A aplicação implementa todas as funcionalidades de um CRUD (**C**reate, **R**ead, **U**pdate, **D**elete) e utiliza um arquivo de texto (`.txt`) como forma de armazenamento de dados, sem a necessidade de um banco de dados relacional.

A interface foi projetada para ser limpa e intuitiva, combinando o formulário de cadastro/alteração e a lista de disciplinas em uma única tela para um fluxo de trabalho eficiente.

## Funcionalidades

* **Criar (Create):** Adicionar novas disciplinas com nome, sigla e carga horária.
* **Ler (Read):** Listar todas as disciplinas cadastradas em uma tabela organizada.
* **Alterar (Update):** Editar as informações de uma disciplina existente. O formulário é preenchido automaticamente com os dados atuais ao clicar em "Alterar".
* **Excluir (Delete):** Remover uma disciplina do sistema de forma permanente.


## Tecnologias Utilizadas

* **Backend:** PHP 8+
* **Frontend:** HTML5 e CSS3
* **Persistência de Dados:** Arquivo de texto (`disciplinas.txt`) com dados delimitados por ponto e vírgula (formato similar a CSV).


## Como Executar o Projeto

### Pré-requisitos

É necessário ter um ambiente de servidor local que suporte PHP, como:
* XAMPP
* WAMP
* MAMP

### Passos para Instalação

1.  Clone ou baixe o repositório para o seu computador.
2.  Copie o arquivo `crud_disciplinas.php` para a pasta raiz do seu servidor web (por exemplo, `C:\xampp\htdocs\` no XAMPP).
3.  Inicie o módulo **Apache** no painel de controle do seu servidor local.
4.  Abra o seu navegador de internet e acesse a URL:
    ```
    http://localhost/crud_disciplinas.php
    ```
5.  O arquivo `disciplinas.txt` será criado automaticamente na mesma pasta assim que a primeira disciplina for salva.


## Decisões de Implementação e Boas Práticas

Este projeto foi construído com foco na simplicidade e na aplicação de conceitos fundamentais de desenvolvimento web com PHP.

* **Estrutura em Arquivo Único:** A lógica do backend (PHP) e a interface (HTML/CSS) foram mantidas em um único arquivo. Esta abordagem facilita a compreensão do fluxo de dados completo da requisição do usuário, passando pelo processamento no servidor, até a exibição da resposta.

* **Segurança Básica:** A função `htmlspecialchars()` é utilizada em toda a exibição de dados que vêm do usuário. Esta é uma prática de segurança fundamental para prevenir ataques de Cross-Site Scripting (XSS), garantindo que nenhum código malicioso seja executado no navegador.

* **Padrão Post-Redirect-Get (PRG):** Após cada operação de escrita (criar, alterar, excluir), o usuário é redirecionado. Isso previne que o formulário seja reenviado acidentalmente ao atualizar a página, tornando a aplicação mais robusta e a experiência do usuário mais fluida.

* **Experiência do Usuário (UX):** O formulário é "pegajoso" (*sticky*), ou seja, ele mantém os dados preenchidos em caso de erro de validação e é preenchido automaticamente no modo de edição. Além disso, mensagens claras de sucesso e erro são exibidas para orientar o usuário.
