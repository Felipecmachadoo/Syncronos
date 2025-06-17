<?php
require_once '../controller/ServicoController.php';

$controller = new ServicoController();

session_start();

// Verifica se está logado
if (!isset($_SESSION['usuario_id'])) {
  header('Location: ../auth/login.php');
  exit;
}

// Verifica se é administrador
if (!isset($_SESSION['Tipo']) || $_SESSION['Tipo'] !== 'administrador') {
  header('Location: ../acesso-negado.php'); // ou redirecione onde quiser
  exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link
    rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
  <link rel="stylesheet" href="../assets/css/sidebar.css">
  <link rel="stylesheet" href="../assets/css/servico.css">
  <title>Serviços - Syncronos</title>
</head>

<body>
  <aside class="sidebar">
    <div class="sidebar-header">
      <img src="../assets/images/Syncronos-branco.png" alt="Logo-Syncronos" />
      <h2>Syncronos</h2>
    </div>
    <ul class="sidebar-links">
      <h4>
        <span>Principal</span>
        <div class="menu-separator"></div>
      </h4>

      <li>
        <a href="dashboard.php">
          <span class="material-symbols-outlined"> dashboard </span>
          Dashboard
        </a>
      </li>

      <li>
        <a href="calendario.php">
          <span class="material-symbols-outlined">calendar_month</span>
          Calendário
        </a>
      </li>

      <h4>
        <span>Cadastros</span>
        <div class="menu-separator"></div>
      </h4>

      <li>
        <a href="horario.php">
          <span class="material-symbols-outlined">search_activity</span>
          Horários
        </a>
      </li>

      <li>
        <a href="servico.php">
          <span class="material-symbols-outlined">build</span> Serviços
        </a>
      </li>

      <li>
        <a href="profissional.php">
          <span class="material-symbols-outlined">person_add</span>
          Profissionais
        </a>
      </li>

      <h4>
        <span>Conta</span>
        <div class="menu-separator"></div>
      </h4>

      <li>
        <a href="#!/perfil">
          <span class="material-symbols-outlined">account_circle</span> Perfil
        </a>
      </li>

      <li>
        <a href="#!/configuracoes">
          <span class="material-symbols-outlined">settings</span>
          Configurações
        </a>
      </li>

      <li>
        <a href="../auth/logout.php">
          <span class="material-symbols-outlined">logout</span> Sair
        </a>
      </li>
    </ul>
    <div class="user-account">
      <div class="user-profile">
        <img src="../assets/images/logo300.png" alt="Foto-de-perfil" />
        <div class="user-detail">
          <h3>Dark Prete's</h3>
          <span>Administrador</span>
        </div>
      </div>
    </div>
  </aside>

  <div class="home-content" id="home-content">
    <div class="servico-button-container">
      <button class="btn-adicionar" id="btnAdicionar">+ Novo Serviço</button>
    </div>

    <div class="servico-lista-container">
      <table class="servico-tabela">
        <thead>
          <tr>
            <th>Nome</th>
            <th>Descrição</th>
            <th>Preço</th>
            <th>Duração</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $servicos = $controller->listarServicos();
          if (empty($servicos)): ?>
            <tr>
              <td colspan="5">Nenhum serviço cadastrado</td>
            </tr>
            <?php else:
            foreach ($servicos as $servico):
              $precoFormatado = 'R$ ' . number_format($servico['Preco'], 2, ',', '.');
            ?>
              <tr>
                <td><?php echo htmlspecialchars($servico['Nome']); ?></td>
                <td><?php echo htmlspecialchars($servico['Descricao']); ?></td>
                <td><?php echo $precoFormatado; ?></td>
                <td><?php echo htmlspecialchars($servico['Duracao']); ?></td>
                <td>
                  <button class="btn-editar" data-id="<?php echo $servico['idServico']; ?>">
                    Editar
                  </button>
                  <button class="btn-excluir" data-id="<?php echo $servico['idServico']; ?>">
                    Excluir
                  </button>
                </td>
              </tr>
          <?php endforeach;
          endif; ?>
        </tbody>
      </table>
    </div>

    <div class="offcanvas-overlay" id="offcanvasOverlay"></div>
    <div class="offcanvas" id="offcanvas">
      <div class="offcanvas-header">
        <h2 class="offcanvas-title">Adicionar Novo Serviço</h2>
        <p class="offcanvas-subtitle">
          Preencha os detalhes do serviço que deseja adicionar.
        </p>
      </div>
      <div class="offcanvas-body">
        <form id="formServico" method="POST" action="../public/ServicoRoutes.php">
          <input type="hidden" name="rota" value="salvarServico" />
          <div class="form-group">
            <label for="nome" class="form-label">Nome do Serviço</label>
            <input
              type="text"
              id="nome"
              name="nome"
              class="form-control"
              placeholder="Digite o nome do serviço"
              autocomplete="off"
              required />
          </div>

          <div class="form-group">
            <label for="preco" class="form-label">Preço</label>
            <input
              type="text"
              id="preco"
              name="preco"
              class="form-control"
              placeholder="R$ 00,00"
              required />
          </div>

          <label for="duration" class="form-label">Duração do Serviço</label>
          <div class="dropdown">
            <div class="dropdown-toggle" id="selected-duration">5 Min</div>
            <div class="dropdown-menu">
              <!-- As opções serão geradas automaticamente via JavaScript -->
            </div>
          </div>

          <input
            type="hidden"
            id="duration-input"
            name="duracao"
            value="5 Min" />

          <div class="form-group">
            <label for="descricao" class="form-label">Descrição</label>
            <textarea
              id="descricao"
              class="form-control"
              name="descricao"
              placeholder="Essa descrição será exibida para o cliente quando ele for escolher o serviço."></textarea>
          </div>
      </div>

      <div class="offcanvas-footer">
        <button class="btn btn-cancelar" id="btnCancelar">Cancelar</button>
        <button type="submit" class="btn btn-salvar" id="btnSalvar">Salvar</button>
      </div>
      </form>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
  <script src="../assets/js/servico.js"></script>
</body>

</html>