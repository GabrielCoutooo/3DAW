# Calculadora Funcional em PHP

Um projeto didático de calculadora web desenvolvido com PHP puro, aplicando conceitos de desenvolvimento web backend.

## Descrição do Projeto

Este projeto implementa uma calculadora web totalmente funcional utilizando PHP. A aplicação é capaz de realizar as quatro operações matemáticas básicas (soma, subtração, multiplicação e divisão), tratando as entradas do usuário e exibindo o resultado de forma clara e intuitiva.

O desenvolvimento foi guiado pelos ensinamentos da disciplina de **Desenvolvimento de Aplicações Web**, com o objetivo de criar um código que fosse não apenas funcional, mas também limpo, seguro e de fácil manutenção.

## Funcionalidades

* **Operações Matemáticas Básicas:** Soma, Subtração, Multiplicação e Divisão.
* **Interface Simples e Intuitiva:** Um formulário limpo construído com HTML e estilizado com CSS.
* **Validação de Dados no Servidor:** O sistema verifica se os valores inseridos são numéricos e se todos os campos foram preenchidos.
* **Tratamento de Erros:** Prevenção contra operações inválidas, como a divisão por zero.
* **Formulário "Sticky":** A calculadora mantém os últimos números e a operação selecionada após o envio, melhorando a experiência do usuário.
* **Código Unificado:** A lógica PHP e a interface (HTML/CSS) estão em um único arquivo para facilitar a compreensão do fluxo de dados completo.

## Tecnologias Utilizadas

* **PHP 8+**
* **HTML5**
* **CSS3**

## Como Executar

1.  **Pré-requisitos:** É necessário ter um ambiente de servidor local configurado, como XAMPP, WAMP ou MAMP.

2.  **Instalação:**
    * Clone ou baixe este repositório.
    * Copie o arquivo `calculadora.php` para a pasta raiz do seu servidor web (geralmente `htdocs` no XAMPP).

3.  **Acesso:**
    * Inicie o serviço Apache através do painel do seu servidor local.
    * Abra seu navegador e acesse a URL: `http://localhost/calculadora.php`.

## Decisões de Implementação e Práticas Profissionais

Seguindo as boas práticas e os conceitos vistos em sala de aula, as seguintes decisões foram tomadas:

* **Validação no Lado do Servidor (Server-Side):** Toda a validação dos dados é realizada no backend com PHP. Isso garante a integridade da aplicação, pois validações no lado do cliente (frontend) podem ser facilmente contornadas.

* **Segurança com `htmlspecialchars()`:** Para prevenir ataques de Cross-Site Scripting (XSS), toda a saída de dados que vieram do usuário (números digitados e o resultado final) é tratada com a função `htmlspecialchars()`. Isso garante que nenhum código malicioso seja renderizado na página.

* **Experiência do Usuário (UX):** O formulário foi implementado de forma a "lembrar" os últimos valores inseridos. Esta prática, conhecida como "Sticky Form", melhora a usabilidade e permite que o usuário realize cálculos sequenciais de forma mais eficiente.
