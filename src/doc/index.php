<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Documentação do Meet2IC</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .screenshot {
      border: 1px solid #ccc;
      margin-bottom: 30px;
      padding: 10px;
      background-color: #f9f9f9;
    }
    .screenshot img {
      max-width: 100%;
      height: auto;
    }
    .step-title {
      color: #0d6efd;
    }
  </style>
</head>
<body class="container py-4">

  <h1 class="mb-4 text-center">Documentação - Meet2IC</h1>

  <!-- Menu de Navegação -->
  <nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top mb-4 shadow-sm">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">📘 Meet2IC</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarMenu">
        <ul class="navbar-nav me-auto">
          <li class="nav-item"><a class="nav-link" href="#criando-enquete">Criando Enquete</a></li>
          <li class="nav-item"><a class="nav-link" href="#respondendo-enquete">Respondendo Enquete</a></li>
          <li class="nav-item"><a class="nav-link" href="#emails">Recebimento de Respostas</a></li>
        </ul>
      </div>
    </div>
  </nav>

<hr>
  <h3 id="criando-enquete" class="mb-5 text-right">Criando Enquete de Disponibilidade</h3>

  <div class="screenshot">
    <h4 class="step-title">1. Tela de Login</h4>
    <p>O usuário acessa o sistema e visualiza a opção de login com Google.</p>
    <img src="images/01-tela_login.png" alt="Tela de login">
  </div>

  <div class="screenshot">
    <h4 class="step-title">2. Autenticação via Google</h4>
    <p>Processo de autenticação OAuth do Google.</p>
    <img src="images/02-auth_google.png" alt="Autenticação Google">
  </div>

  <div class="screenshot">
    <h4 class="step-title">3. Criar Enquete</h4>
    <p>Após autenticado, o usuário pode criar uma nova enquete, informando o título, uma descrição (opcional) e os horários disponíveis.</p>
    <img src="images/03-criar_enquete.png" alt="Criar enquete">
  </div>

  <div class="screenshot">
    <h4 class="step-title">4. Excluir horário errado</h4>
    <p>O usuário pode remover blocos de horário adicionados por engano antes de criar a enquete.</p>
    <img src="images/04-excluir_horario_errado.png" alt="Excluir horário">
  </div>

  <div class="screenshot">
    <h4 class="step-title">5. Botão Criar Enquete</h4>
    <p>Ao concluir, o usuário clica em "Criar Enquete".</p>
    <img src="images/05-botao_criar_enquete.png" alt="Botão criar">
  </div>

  <div class="screenshot">
    <h4 class="step-title">6. Link da Enquete Após Criação</h4>
    <p>Após criada, o link da enquete é gerado e pode ser compartilhado.</p>
    <img src="images/06-link_da_enquete_pos_criacao.png" alt="Link da enquete">
  </div>

<hr>
  <h3 id="respondendo-enquete" class="mb-5 text-right">Respondendo Enquete (disponibilidade dos participantes)</h3>

  <div class="screenshot">
    <h4 class="step-title">7. Acesso sem autenticação</h4>
    <p>Um participante pode acessar a enquete sem estar logado.</p>
    <img src="images/07-acesso_enquete_sem_autenticado.png" alt="Acesso sem login">
  </div>

  <div class="screenshot">
    <h4 class="step-title">8. Responder sem autenticação</h4>
    <p>O participante informa nome e e-mail e escolhe a disponibilidade (Disponível, Talvez, Indisponível).</p>
    <img src="images/08-respondendo_enquete_sem_autenticado.png" alt="Resposta sem login">
  </div>

  <div class="screenshot">
    <h4 class="step-title">9. Resumo após resposta (sem login)</h4>
    <p>Resumo da enquete é mostrado, com o melhor horário em destaque (verde).</p>
    <img src="images/09-resumo_enquete_sem_autenticado.png" alt="Resumo sem login">
  </div>

  <div class="screenshot">
    <h4 class="step-title">10. Responder com autenticação</h4>
    <p>O participante logado responde a enquete diretamente.</p>
    <img src="images/10-respondendo_enquete_autenticado.png" alt="Responder autenticado">
  </div>

  <div class="screenshot">
    <h4 class="step-title">11. Resumo com resposta autenticada</h4>
    <p>Disponibilidade marcada aparece no final e o melhor horário continua destacado.</p>
    <img src="images/11-resumo_enquete-autenticado_disponibilidade_marcada.png" alt="Resumo autenticado">
  </div>

<hr>
  <h3 id="emails" class="mb-5 text-right">Recebimento das Respostas e Interações com a Enquete</h3>

  <div class="screenshot">
    <h4 class="step-title">12 e 13. E-mails enviados ao criador</h4>
    <p>O criador da enquete recebe notificações por e-mail a cada nova interação.</p>
    <img src="images/12-e-mail_recebido_interacao_enquete.png" alt="E-mail 1">
    <img src="images/13-e-mail_recebido_interacao_enquete-2.png" alt="E-mail 2">
  </div>
  
  <div class="screenshot">
    <h4 class="step-title">Fim. Resumo com resposta autenticada</h4>
    <p>Disponibilidade marcada aparece no final e o melhor horário continua destacado em verde.</p>
    <img src="images/11-resumo_enquete-autenticado_disponibilidade_marcada.png" alt="Resumo autenticado">
  </div>

  <footer class="text-center mt-5">
    <hr>
    <p>&copy; <?= date('Y') ?> Meet2IC - Instituto de Computação / UNICAMP</p>
  </footer>

</body>
</html>

