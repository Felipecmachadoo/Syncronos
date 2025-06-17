<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
  header('Location: ../auth/login.php');
  exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
  <link rel="stylesheet" href="../assets/css/barbearia.css" />
  <title>Dark Prete's - Syncronos</title>
</head>

<body>
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
          <div class="user-dropdown-menu" id="userDropdownMenu">
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
        <div class="hours-section">
          <button class="hours-toggle" onclick="toggleHours()">
            Horários de funcionamento ▼
          </button>
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

        <!-- Service Cards -->
        <div class="service-card">
          <div class="service-info">
            <h3>Barboterapia</h3>
            <p class="service-description">
              É um procedimento que tem como objetivo promover o relaxamento através da toalha...
            </p>
            <div class="service-price">R$ 40,00</div>
          </div>
          <button class="btn-service-schedule">Agendar</button>
        </div>

        <div class="service-card">
          <div class="service-info">
            <h3>Botox Capilar</h3>
            <p class="service-description">
              É um tratamento estético que hidrata, tira o frizz e reduz o volume dos cabelos...
            </p>
            <div class="service-price">R$ 80,00</div>
          </div>
          <button class="btn-service-schedule">Agendar</button>
        </div>

        <div class="service-card">
          <div class="service-info">
            <h3>Cabelo e Barboterapia</h3>
            <p class="service-description">
              Nosso combo você alinha o corte do cabelo e a barba com higienização + toalha quente...
            </p>
            <div class="service-price">R$ 80,00</div>
          </div>
          <button class="btn-service-schedule">Agendar</button>
        </div>

        <div class="service-card">
          <div class="service-info">
            <h3>Cabelo, Barba e Sobrancelha</h3>
            <p class="service-description">
              Combo de corte de cabelo + barboterapia + limpeza sobrancelha na navalha...
            </p>
            <div class="service-price">R$ 90,00</div>
          </div>
          <button class="btn-service-schedule">Agendar</button>
        </div>

        <div class="service-card">
          <div class="service-info">
            <h3>Camuflagem dos Fios Brancos</h3>
            <p class="service-description">
              A Camuflagem dos fios brancos traz a naturalidade dos fios escuros de volta, pod...
            </p>
            <div class="service-price">R$ 35,00</div>
          </div>
          <button class="btn-service-schedule">Agendar</button>
        </div>

      </div>
    </div>
  </div>

  <script src="../assets/js/barbearia.js"></script>
</body>

</html>