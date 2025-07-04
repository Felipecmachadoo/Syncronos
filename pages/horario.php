<?php
require_once '../controller/HorarioController.php';
$controller = new HorarioController();
$horariosSalvos = $controller->buscarHorarios();

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
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css" />
  <link rel="stylesheet" href="../assets/css/sidebar.css">
  <link rel="stylesheet" href="../assets/css/horario.css">
  <title>Horários - Syncronos</title>
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

  <div class="home-content">
    <form id="horario-form" method="POST" action="../public/HorarioRoutes.php">
      <input type="hidden" name="rota" value="salvarHorario" />
      <h1 class="titulo-horario">Horários de Funcionamento</h1>
      <div class="grid-container">
        <!-- Coluna 1: Dias de Atendimento -->
        <div class="grid-column">
          <div class="column-header">Dias de Atendimento</div>

          <!-- Segunda-feira -->
          <div class="column-item">
            <div class="checkbox-wrapper-46">
              <input
                type="checkbox"
                id="segunda"
                class="inp-cbx"
                name="dias[]"
                value="segunda" />
              <label for="segunda" class="cbx">
                <span>
                  <svg viewBox="0 0 12 10" height="10px" width="12px">
                    <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                  </svg>
                </span>
                <span>Segunda-feira</span>
              </label>
            </div>
          </div>

          <!-- Terça-feira -->
          <div class="column-item">
            <div class="checkbox-wrapper-46">
              <input
                type="checkbox"
                id="terca"
                class="inp-cbx"
                name="dias[]"
                value="terca" />
              <label for="terca" class="cbx">
                <span>
                  <svg viewBox="0 0 12 10" height="10px" width="12px">
                    <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                  </svg>
                </span>
                <span>Terça-feira</span>
              </label>
            </div>
          </div>

          <!-- Quarta-feira -->
          <div class="column-item">
            <div class="checkbox-wrapper-46">
              <input
                type="checkbox"
                id="quarta"
                class="inp-cbx"
                name="dias[]"
                value="quarta" />
              <label for="quarta" class="cbx">
                <span>
                  <svg viewBox="0 0 12 10" height="10px" width="12px">
                    <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                  </svg>
                </span>
                <span>Quarta-feira</span>
              </label>
            </div>
          </div>

          <!-- Quinta-feira -->
          <div class="column-item">
            <div class="checkbox-wrapper-46">
              <input
                type="checkbox"
                id="quinta"
                class="inp-cbx"
                name="dias[]"
                value="quinta" />
              <label for="quinta" class="cbx">
                <span>
                  <svg viewBox="0 0 12 10" height="10px" width="12px">
                    <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                  </svg>
                </span>
                <span>Quinta-feira</span>
              </label>
            </div>
          </div>

          <!-- Sexta-feira -->
          <div class="column-item">
            <div class="checkbox-wrapper-46">
              <input
                type="checkbox"
                id="sexta"
                class="inp-cbx"
                name="dias[]"
                value="sexta" />
              <label for="sexta" class="cbx">
                <span>
                  <svg viewBox="0 0 12 10" height="10px" width="12px">
                    <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                  </svg>
                </span>
                <span>Sexta-feira</span>
              </label>
            </div>
          </div>

          <!-- Sábado -->
          <div class="column-item">
            <div class="checkbox-wrapper-46">
              <input
                type="checkbox"
                id="sabado"
                class="inp-cbx"
                name="dias[]"
                value="sabado" />
              <label for="sabado" class="cbx">
                <span>
                  <svg viewBox="0 0 12 10" height="10px" width="12px">
                    <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                  </svg>
                </span>
                <span>Sábado</span>
              </label>
            </div>
          </div>

          <!-- Domingo -->
          <div class="column-item">
            <div class="checkbox-wrapper-46">
              <input
                type="checkbox"
                id="domingo"
                class="inp-cbx"
                name="dias[]"
                value="domingo" />
              <label for="domingo" class="cbx">
                <span>
                  <svg viewBox="0 0 12 10" height="10px" width="12px">
                    <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                  </svg>
                </span>
                <span>Domingo</span>
              </label>
            </div>
          </div>
        </div>

        <!-- Coluna 2: Horário de Abertura -->
        <div class="grid-column">
          <div class="column-header">Horário de Abertura</div>

          <!-- Segunda-feira -->
          <div class="column-item">
            <div class="dropdown-container">
              <div class="input-icon-wrapper">
                <input
                  type="text"
                  class="search-input horario-input"
                  placeholder="00:00h"
                  onfocus="showDropdown(event)"
                  onkeyup="filterDropdown(event)"
                  autocomplete="off"
                  data-day="segunda"
                  data-type="abertura"
                  name="abertura_segunda" />
                <span class="input-icon">
                  <span class="material-symbols-outlined" id="dropdown-icon">
                    keyboard_arrow_down
                  </span>
                </span>
              </div>

              <div
                class="dropdown-options horario-dropdown"
                id="dropdown-segunda-abertura">
                <!-- opções geradas via JS -->
              </div>
            </div>
          </div>

          <!-- Terça-feira -->
          <div class="column-item">
            <div class="dropdown-container">
              <div class="input-icon-wrapper">
                <input
                  type="text"
                  class="search-input horario-input"
                  placeholder="00:00h"
                  onfocus="showDropdown(event)"
                  onkeyup="filterDropdown(event)"
                  autocomplete="off"
                  data-day="terca"
                  data-type="abertura"
                  name="abertura_terca" />
                <span class="input-icon">
                  <span class="material-symbols-outlined" id="dropdown-icon">
                    keyboard_arrow_down
                  </span>
                </span>
              </div>

              <div
                class="dropdown-options horario-dropdown"
                id="dropdown-terca-abertura">
                <!-- opções geradas via JS -->
              </div>
            </div>
          </div>

          <!-- Quarta-feira -->
          <div class="column-item">
            <div class="dropdown-container">
              <div class="input-icon-wrapper">
                <input
                  type="text"
                  class="search-input horario-input"
                  placeholder="00:00h"
                  onfocus="showDropdown(event)"
                  onkeyup="filterDropdown(event)"
                  autocomplete="off"
                  data-day="quarta"
                  data-type="abertura"
                  name="abertura_quarta" />
                <span class="input-icon">
                  <span class="material-symbols-outlined" id="dropdown-icon">
                    keyboard_arrow_down
                  </span>
                </span>
              </div>

              <div
                class="dropdown-options horario-dropdown"
                id="dropdown-quarta-abertura">
                <!-- opções geradas via JS -->
              </div>
            </div>
          </div>

          <!-- Quinta-feira -->
          <div class="column-item">
            <div class="dropdown-container">
              <div class="input-icon-wrapper">
                <input
                  type="text"
                  class="search-input horario-input"
                  placeholder="00:00h"
                  onfocus="showDropdown(event)"
                  onkeyup="filterDropdown(event)"
                  autocomplete="off"
                  data-day="quinta"
                  data-type="abertura"
                  name="abertura_quinta" />
                <span class="input-icon">
                  <span class="material-symbols-outlined" id="dropdown-icon">
                    keyboard_arrow_down
                  </span>
                </span>
              </div>

              <div
                class="dropdown-options horario-dropdown"
                id="dropdown-quinta-abertura">
                <!-- opções geradas via JS -->
              </div>
            </div>
          </div>

          <!-- Sexta-feira -->
          <div class="column-item">
            <div class="dropdown-container">
              <div class="input-icon-wrapper">
                <input
                  type="text"
                  class="search-input horario-input"
                  placeholder="00:00h"
                  onfocus="showDropdown(event)"
                  onkeyup="filterDropdown(event)"
                  autocomplete="off"
                  data-day="sexta"
                  data-type="abertura"
                  name="abertura_sexta" />
                <span class="input-icon">
                  <span class="material-symbols-outlined" id="dropdown-icon">
                    keyboard_arrow_down
                  </span>
                </span>
              </div>

              <div
                class="dropdown-options horario-dropdown"
                id="dropdown-sexta-abertura">
                <!-- opções geradas via JS -->
              </div>
            </div>
          </div>

          <!-- Sábado -->
          <div class="column-item">
            <div class="dropdown-container">
              <div class="input-icon-wrapper">
                <input
                  type="text"
                  class="search-input horario-input"
                  placeholder="00:00h"
                  onfocus="showDropdown(event)"
                  onkeyup="filterDropdown(event)"
                  autocomplete="off"
                  data-day="sabado"
                  data-type="abertura"
                  name="abertura_sabado" />
                <span class="input-icon">
                  <span class="material-symbols-outlined" id="dropdown-icon">
                    keyboard_arrow_down
                  </span>
                </span>
              </div>

              <div
                class="dropdown-options horario-dropdown"
                id="dropdown-sabado-abertura">
                <!-- opções geradas via JS -->
              </div>
            </div>
          </div>

          <!-- Domingo -->
          <div class="column-item">
            <div class="dropdown-container">
              <div class="input-icon-wrapper">
                <input
                  type="text"
                  class="search-input horario-input"
                  placeholder="00:00h"
                  onfocus="showDropdown(event)"
                  onkeyup="filterDropdown(event)"
                  autocomplete="off"
                  data-day="domingo"
                  data-type="abertura"
                  name="abertura_domingo" />
                <span class="input-icon">
                  <span class="material-symbols-outlined" id="dropdown-icon">
                    keyboard_arrow_down
                  </span>
                </span>
              </div>

              <div
                class="dropdown-options horario-dropdown"
                id="dropdown-domingo-abertura">
                <!-- opções geradas via JS -->
              </div>
            </div>
          </div>
        </div>

        <!-- Coluna 3: Horário de Fechamento -->
        <div class="grid-column">
          <div class="column-header">Horário de Fechamento</div>

          <!-- Segunda-feira -->
          <div class="column-item">
            <div class="dropdown-container">
              <div class="input-icon-wrapper">
                <input
                  type="text"
                  class="search-input horario-input"
                  placeholder="00:00h"
                  onfocus="showDropdown(event)"
                  onkeyup="filterDropdown(event)"
                  autocomplete="off"
                  data-day="segunda"
                  data-type="fechamento"
                  name="fechamento_segunda" />
                <span class="input-icon">
                  <span class="material-symbols-outlined" id="dropdown-icon">
                    keyboard_arrow_down
                  </span>
                </span>
              </div>

              <div
                class="dropdown-options horario-dropdown"
                id="dropdown-segunda-fechamento">
                <!-- opções geradas via JS -->
              </div>
            </div>
          </div>

          <!-- Terça-feira -->
          <div class="column-item">
            <div class="dropdown-container">
              <div class="input-icon-wrapper">
                <input
                  type="text"
                  class="search-input horario-input"
                  placeholder="00:00h"
                  onfocus="showDropdown(event)"
                  onkeyup="filterDropdown(event)"
                  autocomplete="off"
                  data-day="terca"
                  data-type="fechamento"
                  name="fechamento_terca" />
                <span class="input-icon">
                  <span class="material-symbols-outlined" id="dropdown-icon">
                    keyboard_arrow_down
                  </span>
                </span>
              </div>

              <div
                class="dropdown-options horario-dropdown"
                id="dropdown-terca-fechamento">
                <!-- opções geradas via JS -->
              </div>
            </div>
          </div>

          <!-- Quarta-feira -->
          <div class="column-item">
            <div class="dropdown-container">
              <div class="input-icon-wrapper">
                <input
                  type="text"
                  class="search-input horario-input"
                  placeholder="00:00h"
                  onfocus="showDropdown(event)"
                  onkeyup="filterDropdown(event)"
                  autocomplete="off"
                  data-day="quarta"
                  data-type="fechamento"
                  name="fechamento_quarta" />
                <span class="input-icon">
                  <span class="material-symbols-outlined" id="dropdown-icon">
                    keyboard_arrow_down
                  </span>
                </span>
              </div>

              <div
                class="dropdown-options horario-dropdown"
                id="dropdown-quarta-fechamento">
                <!-- opções geradas via JS -->
              </div>
            </div>
          </div>

          <!-- Quinta-feira -->
          <div class="column-item">
            <div class="dropdown-container">
              <div class="input-icon-wrapper">
                <input
                  type="text"
                  class="search-input horario-input"
                  placeholder="00:00h"
                  onfocus="showDropdown(event)"
                  onkeyup="filterDropdown(event)"
                  autocomplete="off"
                  data-day="quinta"
                  data-type="fechamento"
                  name="fechamento_quinta" />
                <span class="input-icon">
                  <span class="material-symbols-outlined" id="dropdown-icon">
                    keyboard_arrow_down
                  </span>
                </span>
              </div>

              <div
                class="dropdown-options horario-dropdown"
                id="dropdown-quinta-fechamento">
                <!-- opções geradas via JS -->
              </div>
            </div>
          </div>

          <!-- Sexta-feira -->
          <div class="column-item">
            <div class="dropdown-container">
              <div class="input-icon-wrapper">
                <input
                  type="text"
                  class="search-input horario-input"
                  placeholder="00:00h"
                  onfocus="showDropdown(event)"
                  onkeyup="filterDropdown(event)"
                  autocomplete="off"
                  data-day="sexta"
                  data-type="fechamento"
                  name="fechamento_sexta" />
                <span class="input-icon">
                  <span class="material-symbols-outlined" id="dropdown-icon">
                    keyboard_arrow_down
                  </span>
                </span>
              </div>

              <div
                class="dropdown-options horario-dropdown"
                id="dropdown-sexta-fechamento">
                <!-- opções geradas via JS -->
              </div>
            </div>
          </div>

          <!-- Sábado -->
          <div class="column-item">
            <div class="dropdown-container">
              <div class="input-icon-wrapper">
                <input
                  type="text"
                  class="search-input horario-input"
                  placeholder="00:00h"
                  onfocus="showDropdown(event)"
                  onkeyup="filterDropdown(event)"
                  autocomplete="off"
                  data-day="sabado"
                  data-type="fechamento"
                  name="fechamento_sabado" />
                <span class="input-icon">
                  <span class="material-symbols-outlined" id="dropdown-icon">
                    keyboard_arrow_down
                  </span>
                </span>
              </div>

              <div
                class="dropdown-options horario-dropdown"
                id="dropdown-sabado-fechamento">
                <!-- opções geradas via JS -->
              </div>
            </div>
          </div>

          <!-- Domingo -->
          <div class="column-item">
            <div class="dropdown-container">
              <div class="input-icon-wrapper">
                <input
                  type="text"
                  class="search-input horario-input"
                  placeholder="00:00h"
                  onfocus="showDropdown(event)"
                  onkeyup="filterDropdown(event)"
                  autocomplete="off"
                  data-day="domingo"
                  data-type="fechamento"
                  name="fechamento_domingo" />
                <span class="input-icon">
                  <span class="material-symbols-outlined" id="dropdown-icon">
                    keyboard_arrow_down
                  </span>
                </span>
              </div>

              <div
                class="dropdown-options horario-dropdown"
                id="dropdown-domingo-fechamento">
                <!-- opções geradas via JS -->
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Botão de Salvar -->
      <div class="button-container">
        <button type="submit" id="save-button" class="save-button">
          Salvar Horários
        </button>
      </div>

      <!-- Mensagem de feedback -->
      <div id="feedback-message" class="feedback-message"></div>
    </form>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
  <script src="../assets/js/horario.js"></script>

  <script>
    const dadosDoBanco = <?php echo json_encode($horariosSalvos); ?>;
  </script>

</body>

</html>