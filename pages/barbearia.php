<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
  header('Location: ../auth/login.php');
  exit;
}

require_once '../controller/ServicoController.php';

$servicoController = new ServicoController();
$servicos = $servicoController->listarServicos();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="../assets/css/barbearia.css" />
  <title>Dark Prete's - Syncronos</title>
</head>

<body data-user-id="<?= $_SESSION['usuario_id'] ?>">
  <!-- Header -->
  <header class="header">
    <div class="logo">
      <img src="../assets/images/Syncronos-branco.png" alt="Syncronos" class="logo-img" />
      <h2>Syncronos</h2>
    </div>
    <nav>
      <ul class="nav-menu">
        <li><a href="#">Salões de beleza</a></li>
        <li><a href="#">Clínica de estética</a></li>
        <li><a href="#">Estúdio de tatuagem</a></li>
        <li><a href="#">Barbearias</a></li>
        <li><a href="#">Espaços infantis</a></li>
      </ul>
    </nav>

    <!-- Header Actions -->
    <div class="header-actions">
      <div class="search-icon">
        <span class="material-symbols-outlined">search</span>
      </div>
      <a href="#" class="btn-outline">Seu negócio</a>

      <!-- Verifica se está logado -->
      <?php if (isset($_SESSION['usuario_id'])): ?>
        <div class="user-dropdown">
          <button class="user-dropdown-toggle" onclick="toggleUserDropdown()">
            <span class="user-name">
              <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>
            </span>
            <span class="material-symbols-outlined dropdown-arrow">keyboard_arrow_down</span>
          </button>
          <div class="user-dropdown-menu" id="userDropdownMenu" style="display: none;">
            <a href="#" class="dropdown-item">
              <span class="material-symbols-outlined">event_note</span>
              Meus Agendamentos
            </a>
            <a href="../auth/logout.php" class="dropdown-item">
              <span class="material-symbols-outlined">logout</span>
              Sair
            </a>
          </div>
        </div>
      <?php else: ?>
        <a href="../auth/login.php" class="btn-primary">Entrar</a>
      <?php endif; ?>
    </div>
  </header>

  <!-- Business Header -->
  <div class="business-header">
    <div class="business-info">
      <div class="business-logo">
        <img src="../assets/images/logo300.png" alt="Dark Prete's" class="business-logo-img" />
      </div>
      <div class="business-details">
        <h1>Dark Prete's</h1>
        <div class="description">
          Atendimento ágil, individual e pontual por agendamento, ambiente confortável e climatizado.
        </div>
        <div class="address">
          📍 R. Siqueira Campos, 1600 - Vila Roberto, Pres. Prudente - SP
        </div>
        <div class="hours-section" id="hours-section">
          <button class="hours-toggle" id="hours-toggle-btn">Horários de funcionamento ▼</button>
          <div id="hours-list" style="display:none; margin-top:10px;">
            <!-- Horários vão aparecer aqui -->
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Services Section -->
  <div class="services-section">
    <div class="services-header">
      <h2>Serviços oferecidos</h2>
      <p class="services-subtitle">
        Confira tudo o que Dark Prete's tem para você
      </p>
    </div>
    <div class="services-content">
      <div class="services-main">
        <div class="search-box">
          <input type="text" class="search-input" placeholder="Barba, cabelo..." />
          <div class="search-icon-input">
            <span class="material-symbols-outlined">search</span>
          </div>
        </div>

        <?php foreach ($servicos as $servico): ?>
          <div class="service-card" data-servico-id="<?= $servico['idServico'] ?>">
            <div class="service-info">
              <h3><?= htmlspecialchars($servico['Nome']) ?></h3>
              <p class="service-description">
                <?= htmlspecialchars($servico['Descricao']) ?>
              </p>
              <div class="service-price">
                R$ <?= number_format($servico['Preco'], 2, ',', '.') ?>
              </div>
            </div>
            <button class="btn-agendar btn btn-primary">Agendar</button>
          </div>
        <?php endforeach; ?>

      </div>
    </div>
  </div>

  <div class="modal fade" id="agendarModal" tabindex="-1" aria-labelledby="agendarModalLabel">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="agendarModalLabel">Agendamento</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>
        <div class="modal-body">
          <div id="profissionais-section"></div>
          <div id="datas-section" style="display:none;">
            <h6 id="data-title"></h6>
            <div class="d-flex justify-content-between align-items-center mb-3">
              <button class="btn btn-sm btn-outline-secondary carousel-prev" type="button">
                <span class="material-symbols-outlined">chevron_left</span>
              </button>
              <div class="date-carousel-container" style="overflow-x: auto; white-space: nowrap; flex-grow: 1; margin: 0 10px;">
                <div class="date-carousel d-inline-flex"></div>
              </div>
              <button class="btn btn-sm btn-outline-secondary carousel-next" type="button">
                <span class="material-symbols-outlined">chevron_right</span>
              </button>
            </div>
            <div id="horarios-disponiveis" style="margin-top: 20px;"></div>
            <div class="text-center mt-3">
              <button id="btn-voltar-profissionais" class="btn btn-link">Voltar para profissionais</button>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
        </div>
      </div>
    </div>
  </div>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/js/barbearia.js"></script>
</body>

</html>