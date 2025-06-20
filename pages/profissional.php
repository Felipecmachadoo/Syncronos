<?php
require_once __DIR__ . '/../controller/ProfissionalController.php';

$controller = new ProfissionalController();
$profissionais = $controller->listarProfissionais();

if (isset($_SESSION['sucesso'])) {
  echo '<div class="alert alert-success">' . $_SESSION['sucesso'] . '</div>';
  unset($_SESSION['sucesso']);
}
if (isset($_SESSION['erro'])) {
  echo '<div class="alert alert-danger">' . $_SESSION['erro'] . '</div>';
  unset($_SESSION['erro']);
}

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
  <link rel="stylesheet" href="../assets/css/profissional.css">
  <title>Profissional - Syncronos</title>
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
    <!-- Botão para abrir o offcanvas -->
    <div class="profissional-button-container">
      <button id="profissional-open-offcanvas" class="profissional-open-button">
        + Novo Profissional
      </button>
    </div>

    <div class="profissional-lista-container">
      <table class="profissional-tabela">
        <thead>
          <tr>
            <th>Nome</th>
            <th>Celular</th>
            <th>Profissão</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if (empty($profissionais)) : ?>
            <tr>
              <td colspan="5">Nenhum profissional cadastrado</td>
            </tr>
            <?php else:
            foreach ($profissionais as $prof) : ?>
              <tr>
                <td><?php echo htmlspecialchars($prof['Nome']); ?></td>
                <td><?php echo htmlspecialchars($prof['Celular']); ?></td>
                <td><?php echo htmlspecialchars($prof['Especialidade']); ?></td>
                <td>
                  <button class="btn-editar"
                    data-id="<?php echo $prof['idProfissional']; ?>"
                    data-nome="<?php echo htmlspecialchars($prof['Nome']); ?>"
                    data-celular="<?php echo htmlspecialchars($prof['Celular']); ?>"
                    data-especialidade="<?php echo htmlspecialchars($prof['Especialidade']); ?>">
                    Editar
                  </button>
                  <button class="btn-excluir"
                    data-id="<?php echo $prof['idProfissional']; ?>">
                    Excluir
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>


    <!-- Overlay para fundo escurecido -->
    <div id="profissional-overlay" class="profissional-overlay"></div>

    <!-- Offcanvas -->
    <div id="profissional-offcanvas" class="profissional-offcanvas">
      <!-- Cabeçalho do offcanvas com navegação -->
      <div class="profissional-offcanvas-header">
        <h2>Cadastro de Profissional</h2>
        <div class="profissional-header-actions">
          <button
            id="profissional-close-offcanvas"
            class="profissional-close-button">
            <span class="material-symbols-outlined">close</span>
          </button>
        </div>
      </div>
      <div class="profissional-offcanvas-sections">
        <button
          id="profissional-nav-cadastro"
          class="profissional-nav-tab active">
          <span class="material-symbols-outlined">person</span>
          Cadastro
        </button>
        <button id="profissional-nav-expediente" class="profissional-nav-tab">
          <span class="material-symbols-outlined">schedule</span>
          Expediente
        </button>
      </div>

      <!-- Container para os formulários -->
      <div class="profissional-forms-container">
        <!-- Formulário de cadastro de profissional -->
        <div
          id="profissional-cadastro-form-container"
          class="profissional-form-panel active">
          <div class="profissional-offcanvas-body">
            <form id="profissional-form" method="POST" action="../public/ProfissionalRoutes.php">
              <input type="hidden" name="rota" value="salvarProfissional">
              <div class="profissional-form-group">
                <label for="profissional-nome">Nome</label>
                <input
                  type="text"
                  id="profissional-nome"
                  name="nome"
                  placeholder="Digite o nome completo"
                  required />
              </div>

              <div class="profissional-form-group">
                <label for="profissional-celular">Celular</label>
                <input
                  type="tel"
                  id="profissional-celular"
                  name="celular"
                  placeholder="(00) 00000-0000"
                  required />
              </div>

              <div class="profissional-form-group">
                <label for="profissional-profissao">Profissão</label>
                <input
                  type="text"
                  id="profissional-profissao"
                  name="especialidade"
                  placeholder="Digite a profissão"
                  required />
              </div>
            </form>
          </div>
        </div>

        <!-- Formulário de expediente -->
        <div
          id="profissional-expediente-form-container"
          class="profissional-form-panel">
          <div class="profissional-offcanvas-body">
            <form id="profissional-horario-form" method="POST" action="../public/ProfissionalRoutes.php">
              <input type="hidden" name="rota" value="salvarExpediente">
              <input type="hidden" id="profissional-id-hidden" name="idProfissional">
              <div class="profissional-grid-container">
                <!-- Coluna 1: Dias de Atendimento -->
                <div class="profissional-grid-column">
                  <div class="profissional-column-header">
                    Dias de Atendimento
                  </div>

                  <!-- Segunda-feira -->
                  <div class="profissional-column-item">
                    <div class="profissional-checkbox-wrapper">
                      <input
                        type="checkbox"
                        id="profissional-segunda"
                        class="profissional-inp-cbx"
                        name="profissional-dias[]"
                        value="segunda" />
                      <label
                        for="profissional-segunda"
                        class="profissional-cbx">
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
                  <div class="profissional-column-item">
                    <div class="profissional-checkbox-wrapper">
                      <input
                        type="checkbox"
                        id="profissional-terca"
                        class="profissional-inp-cbx"
                        name="profissional-dias[]"
                        value="terca" />
                      <label for="profissional-terca" class="profissional-cbx">
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
                  <div class="profissional-column-item">
                    <div class="profissional-checkbox-wrapper">
                      <input
                        type="checkbox"
                        id="profissional-quarta"
                        class="profissional-inp-cbx"
                        name="profissional-dias[]"
                        value="quarta" />
                      <label for="profissional-quarta" class="profissional-cbx">
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
                  <div class="profissional-column-item">
                    <div class="profissional-checkbox-wrapper">
                      <input
                        type="checkbox"
                        id="profissional-quinta"
                        class="profissional-inp-cbx"
                        name="profissional-dias[]"
                        value="quinta" />
                      <label for="profissional-quinta" class="profissional-cbx">
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
                  <div class="profissional-column-item">
                    <div class="profissional-checkbox-wrapper">
                      <input
                        type="checkbox"
                        id="profissional-sexta"
                        class="profissional-inp-cbx"
                        name="profissional-dias[]"
                        value="sexta" />
                      <label for="profissional-sexta" class="profissional-cbx">
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
                  <div class="profissional-column-item">
                    <div class="profissional-checkbox-wrapper">
                      <input
                        type="checkbox"
                        id="profissional-sabado"
                        class="profissional-inp-cbx"
                        name="profissional-dias[]"
                        value="sabado" />
                      <label for="profissional-sabado" class="profissional-cbx">
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
                  <div class="profissional-column-item">
                    <div class="profissional-checkbox-wrapper">
                      <input
                        type="checkbox"
                        id="profissional-domingo"
                        class="profissional-inp-cbx"
                        name="profissional-dias[]"
                        value="domingo" />
                      <label
                        for="profissional-domingo"
                        class="profissional-cbx">
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
                <div class="profissional-grid-column">
                  <div class="profissional-column-header">
                    Início do Expediente
                  </div>

                  <!-- Segunda-feira -->
                  <div class="profissional-column-item">
                    <div class="profissional-dropdown-container">
                      <div class="profissional-input-icon-wrapper">
                        <input
                          type="text"
                          class="profissional-search-input profissional-horario-input"
                          placeholder="00:00h"
                          autocomplete="off"
                          data-day="segunda"
                          data-type="abertura"
                          name="profissional-abertura_segunda" />
                        <span class="profissional-input-icon">
                          <span
                            class="material-symbols-outlined"
                            id="profissional-dropdown-icon">
                            keyboard_arrow_down
                          </span>
                        </span>
                      </div>

                      <div
                        class="profissional-dropdown-options profissional-horario-dropdown"
                        id="profissional-dropdown-segunda-abertura">
                        <!-- opções geradas via JS -->
                      </div>
                    </div>
                  </div>

                  <!-- Terça-feira -->
                  <div class="profissional-column-item">
                    <div class="profissional-dropdown-container">
                      <div class="profissional-input-icon-wrapper">
                        <input
                          type="text"
                          class="profissional-search-input profissional-horario-input"
                          placeholder="00:00h"
                          autocomplete="off"
                          data-day="terca"
                          data-type="abertura"
                          name="profissional-abertura_terca" />
                        <span class="profissional-input-icon">
                          <span
                            class="material-symbols-outlined"
                            id="profissional-dropdown-icon">
                            keyboard_arrow_down
                          </span>
                        </span>
                      </div>

                      <div
                        class="profissional-dropdown-options profissional-horario-dropdown"
                        id="profissional-dropdown-terca-abertura">
                        <!-- opções geradas via JS -->
                      </div>
                    </div>
                  </div>

                  <!-- Quarta-feira -->
                  <div class="profissional-column-item">
                    <div class="profissional-dropdown-container">
                      <div class="profissional-input-icon-wrapper">
                        <input
                          type="text"
                          class="profissional-search-input profissional-horario-input"
                          placeholder="00:00h"
                          autocomplete="off"
                          data-day="quarta"
                          data-type="abertura"
                          name="profissional-abertura_quarta" />
                        <span class="profissional-input-icon">
                          <span
                            class="material-symbols-outlined"
                            id="profissional-dropdown-icon">
                            keyboard_arrow_down
                          </span>
                        </span>
                      </div>

                      <div
                        class="profissional-dropdown-options profissional-horario-dropdown"
                        id="profissional-dropdown-quarta-abertura">
                        <!-- opções geradas via JS -->
                      </div>
                    </div>
                  </div>

                  <!-- Quinta-feira -->
                  <div class="profissional-column-item">
                    <div class="profissional-dropdown-container">
                      <div class="profissional-input-icon-wrapper">
                        <input
                          type="text"
                          class="profissional-search-input profissional-horario-input"
                          placeholder="00:00h"
                          autocomplete="off"
                          data-day="quinta"
                          data-type="abertura"
                          name="profissional-abertura_quinta" />
                        <span class="profissional-input-icon">
                          <span
                            class="material-symbols-outlined"
                            id="profissional-dropdown-icon">
                            keyboard_arrow_down
                          </span>
                        </span>
                      </div>

                      <div
                        class="profissional-dropdown-options profissional-horario-dropdown"
                        id="profissional-dropdown-quinta-abertura">
                        <!-- opções geradas via JS -->
                      </div>
                    </div>
                  </div>

                  <!-- Sexta-feira -->
                  <div class="profissional-column-item">
                    <div class="profissional-dropdown-container">
                      <div class="profissional-input-icon-wrapper">
                        <input
                          type="text"
                          class="profissional-search-input profissional-horario-input"
                          placeholder="00:00h"
                          autocomplete="off"
                          data-day="sexta"
                          data-type="abertura"
                          name="profissional-abertura_sexta" />
                        <span class="profissional-input-icon">
                          <span
                            class="material-symbols-outlined"
                            id="profissional-dropdown-icon">
                            keyboard_arrow_down
                          </span>
                        </span>
                      </div>

                      <div
                        class="profissional-dropdown-options profissional-horario-dropdown"
                        id="profissional-dropdown-sexta-abertura">
                        <!-- opções geradas via JS -->
                      </div>
                    </div>
                  </div>

                  <!-- Sábado -->
                  <div class="profissional-column-item">
                    <div class="profissional-dropdown-container">
                      <div class="profissional-input-icon-wrapper">
                        <input
                          type="text"
                          class="profissional-search-input profissional-horario-input"
                          placeholder="00:00h"
                          autocomplete="off"
                          data-day="sabado"
                          data-type="abertura"
                          name="profissional-abertura_sabado" />
                        <span class="profissional-input-icon">
                          <span
                            class="material-symbols-outlined"
                            id="profissional-dropdown-icon">
                            keyboard_arrow_down
                          </span>
                        </span>
                      </div>

                      <div
                        class="profissional-dropdown-options profissional-horario-dropdown"
                        id="profissional-dropdown-sabado-abertura">
                        <!-- opções geradas via JS -->
                      </div>
                    </div>
                  </div>

                  <!-- Domingo -->
                  <div class="profissional-column-item">
                    <div class="profissional-dropdown-container">
                      <div class="profissional-input-icon-wrapper">
                        <input
                          type="text"
                          class="profissional-search-input profissional-horario-input"
                          placeholder="00:00h"
                          autocomplete="off"
                          data-day="domingo"
                          data-type="abertura"
                          name="profissional-abertura_domingo" />
                        <span class="profissional-input-icon">
                          <span
                            class="material-symbols-outlined"
                            id="profissional-dropdown-icon">
                            keyboard_arrow_down
                          </span>
                        </span>
                      </div>

                      <div
                        class="profissional-dropdown-options profissional-horario-dropdown"
                        id="profissional-dropdown-domingo-abertura">
                        <!-- opções geradas via JS -->
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Coluna 3: Início do Intervalo -->
                <div class="profissional-grid-column">
                  <div class="profissional-column-header">
                    Início do Intervalo
                  </div>

                  <!-- Segunda-feira -->
                  <div class="profissional-column-item">
                    <div class="profissional-dropdown-container">
                      <div class="profissional-input-icon-wrapper">
                        <input
                          type="text"
                          class="profissional-search-input profissional-horario-input"
                          placeholder="00:00h"
                          autocomplete="off"
                          data-day="segunda"
                          data-type="inicio_intervalo"
                          name="profissional-inicio_intervalo_segunda" />
                        <span class="profissional-input-icon">
                          <span
                            class="material-symbols-outlined"
                            id="profissional-dropdown-icon">
                            keyboard_arrow_down
                          </span>
                        </span>
                      </div>

                      <div
                        class="profissional-dropdown-options profissional-horario-dropdown"
                        id="profissional-dropdown-segunda-inicio_intervalo">
                        <!-- opções geradas via JS -->
                      </div>
                    </div>
                  </div>

                  <!-- Terça-feira -->
                  <div class="profissional-column-item">
                    <div class="profissional-dropdown-container">
                      <div class="profissional-input-icon-wrapper">
                        <input
                          type="text"
                          class="profissional-search-input profissional-horario-input"
                          placeholder="00:00h"
                          autocomplete="off"
                          data-day="terca"
                          data-type="inicio_intervalo"
                          name="profissional-inicio_intervalo_terca" />
                        <span class="profissional-input-icon">
                          <span
                            class="material-symbols-outlined"
                            id="profissional-dropdown-icon">
                            keyboard_arrow_down
                          </span>
                        </span>
                      </div>

                      <div
                        class="profissional-dropdown-options profissional-horario-dropdown"
                        id="profissional-dropdown-terca-inicio_intervalo">
                        <!-- opções geradas via JS -->
                      </div>
                    </div>
                  </div>

                  <!-- Quarta-feira -->
                  <div class="profissional-column-item">
                    <div class="profissional-dropdown-container">
                      <div class="profissional-input-icon-wrapper">
                        <input
                          type="text"
                          class="profissional-search-input profissional-horario-input"
                          placeholder="00:00h"
                          autocomplete="off"
                          data-day="quarta"
                          data-type="inicio_intervalo"
                          name="profissional-inicio_intervalo_quarta" />
                        <span class="profissional-input-icon">
                          <span
                            class="material-symbols-outlined"
                            id="profissional-dropdown-icon">
                            keyboard_arrow_down
                          </span>
                        </span>
                      </div>

                      <div
                        class="profissional-dropdown-options profissional-horario-dropdown"
                        id="profissional-dropdown-quarta-inicio_intervalo">
                        <!-- opções geradas via JS -->
                      </div>
                    </div>
                  </div>

                  <!-- Quinta-feira -->
                  <div class="profissional-column-item">
                    <div class="profissional-dropdown-container">
                      <div class="profissional-input-icon-wrapper">
                        <input
                          type="text"
                          class="profissional-search-input profissional-horario-input"
                          placeholder="00:00h"
                          autocomplete="off"
                          data-day="quinta"
                          data-type="inicio_intervalo"
                          name="profissional-inicio_intervalo_quinta" />
                        <span class="profissional-input-icon">
                          <span
                            class="material-symbols-outlined"
                            id="profissional-dropdown-icon">
                            keyboard_arrow_down
                          </span>
                        </span>
                      </div>

                      <div
                        class="profissional-dropdown-options profissional-horario-dropdown"
                        id="profissional-dropdown-quinta-inicio_intervalo">
                        <!-- opções geradas via JS -->
                      </div>
                    </div>
                  </div>

                  <!-- Sexta-feira -->
                  <div class="profissional-column-item">
                    <div class="profissional-dropdown-container">
                      <div class="profissional-input-icon-wrapper">
                        <input
                          type="text"
                          class="profissional-search-input profissional-horario-input"
                          placeholder="00:00h"
                          autocomplete="off"
                          data-day="sexta"
                          data-type="inicio_intervalo"
                          name="profissional-inicio_intervalo_sexta" />
                        <span class="profissional-input-icon">
                          <span
                            class="material-symbols-outlined"
                            id="profissional-dropdown-icon">
                            keyboard_arrow_down
                          </span>
                        </span>
                      </div>

                      <div
                        class="profissional-dropdown-options profissional-horario-dropdown"
                        id="profissional-dropdown-sexta-inicio_intervalo">
                        <!-- opções geradas via JS -->
                      </div>
                    </div>
                  </div>

                  <!-- Sábado -->
                  <div class="profissional-column-item">
                    <div class="profissional-dropdown-container">
                      <div class="profissional-input-icon-wrapper">
                        <input
                          type="text"
                          class="profissional-search-input profissional-horario-input"
                          placeholder="00:00h"
                          autocomplete="off"
                          data-day="sabado"
                          data-type="inicio_intervalo"
                          name="profissional-inicio_intervalo_sabado" />
                        <span class="profissional-input-icon">
                          <span
                            class="material-symbols-outlined"
                            id="profissional-dropdown-icon">
                            keyboard_arrow_down
                          </span>
                        </span>
                      </div>

                      <div
                        class="profissional-dropdown-options profissional-horario-dropdown"
                        id="profissional-dropdown-sabado-inicio_intervalo">
                        <!-- opções geradas via JS -->
                      </div>
                    </div>
                  </div>

                  <!-- Domingo -->
                  <div class="profissional-column-item">
                    <div class="profissional-dropdown-container">
                      <div class="profissional-input-icon-wrapper">
                        <input
                          type="text"
                          class="profissional-search-input profissional-horario-input"
                          placeholder="00:00h"
                          autocomplete="off"
                          data-day="domingo"
                          data-type="inicio_intervalo"
                          name="profissional-inicio_intervalo_domingo" />
                        <span class="profissional-input-icon">
                          <span
                            class="material-symbols-outlined"
                            id="profissional-dropdown-icon">
                            keyboard_arrow_down
                          </span>
                        </span>
                      </div>

                      <div
                        class="profissional-dropdown-options profissional-horario-dropdown"
                        id="profissional-dropdown-domingo-inicio_intervalo">
                        <!-- opções geradas via JS -->
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Coluna 4: Fim do Intervalo -->
                <div class="profissional-grid-column" id="profissional-coluna4">
                  <div class="profissional-column-header">Fim do Intervalo</div>

                  <!-- Segunda-feira -->
                  <div class="profissional-column-item">
                    <div class="profissional-dropdown-container">
                      <div class="profissional-input-icon-wrapper">
                        <input
                          type="text"
                          class="profissional-search-input profissional-horario-input"
                          placeholder="00:00h"
                          autocomplete="off"
                          data-day="segunda"
                          data-type="fim_intervalo"
                          name="profissional-fim_intervalo_segunda" />
                        <span class="profissional-input-icon">
                          <span
                            class="material-symbols-outlined"
                            id="profissional-dropdown-icon">
                            keyboard_arrow_down
                          </span>
                        </span>
                      </div>

                      <div
                        class="profissional-dropdown-options profissional-horario-dropdown"
                        id="profissional-dropdown-segunda-fim_intervalo">
                        <!-- opções geradas via JS -->
                      </div>
                    </div>
                  </div>

                  <!-- Terça-feira -->
                  <div class="profissional-column-item">
                    <div class="profissional-dropdown-container">
                      <div class="profissional-input-icon-wrapper">
                        <input
                          type="text"
                          class="profissional-search-input profissional-horario-input"
                          placeholder="00:00h"
                          autocomplete="off"
                          data-day="terca"
                          data-type="fim_intervalo"
                          name="profissional-fim_intervalo_terca" />
                        <span class="profissional-input-icon">
                          <span
                            class="material-symbols-outlined"
                            id="profissional-dropdown-icon">
                            keyboard_arrow_down
                          </span>
                        </span>
                      </div>

                      <div
                        class="profissional-dropdown-options profissional-horario-dropdown"
                        id="profissional-dropdown-terca-fim_intervalo">
                        <!-- opções geradas via JS -->
                      </div>
                    </div>
                  </div>

                  <!-- Quarta-feira -->
                  <div class="profissional-column-item">
                    <div class="profissional-dropdown-container">
                      <div class="profissional-input-icon-wrapper">
                        <input
                          type="text"
                          class="profissional-search-input profissional-horario-input"
                          placeholder="00:00h"
                          autocomplete="off"
                          data-day="quarta"
                          data-type="fim_intervalo"
                          name="profissional-fim_intervalo_quarta" />
                        <span class="profissional-input-icon">
                          <span
                            class="material-symbols-outlined"
                            id="profissional-dropdown-icon">
                            keyboard_arrow_down
                          </span>
                        </span>
                      </div>

                      <div
                        class="profissional-dropdown-options profissional-horario-dropdown"
                        id="profissional-dropdown-quarta-fim_intervalo">
                        <!-- opções geradas via JS -->
                      </div>
                    </div>
                  </div>

                  <!-- Quinta-feira -->
                  <div class="profissional-column-item">
                    <div class="profissional-dropdown-container">
                      <div class="profissional-input-icon-wrapper">
                        <input
                          type="text"
                          class="profissional-search-input profissional-horario-input"
                          placeholder="00:00h"
                          autocomplete="off"
                          data-day="quinta"
                          data-type="fim_intervalo"
                          name="profissional-fim_intervalo_quinta" />
                        <span class="profissional-input-icon">
                          <span
                            class="material-symbols-outlined"
                            id="profissional-dropdown-icon">
                            keyboard_arrow_down
                          </span>
                        </span>
                      </div>

                      <div
                        class="profissional-dropdown-options profissional-horario-dropdown"
                        id="profissional-dropdown-quinta-fim_intervalo">
                        <!-- opções geradas via JS -->
                      </div>
                    </div>
                  </div>

                  <!-- Sexta-feira -->
                  <div class="profissional-column-item">
                    <div class="profissional-dropdown-container">
                      <div class="profissional-input-icon-wrapper">
                        <input
                          type="text"
                          class="profissional-search-input profissional-horario-input"
                          placeholder="00:00h"
                          autocomplete="off"
                          data-day="sexta"
                          data-type="fim_intervalo"
                          name="profissional-fim_intervalo_sexta" />
                        <span class="profissional-input-icon">
                          <span
                            class="material-symbols-outlined"
                            id="profissional-dropdown-icon">
                            keyboard_arrow_down
                          </span>
                        </span>
                      </div>

                      <div
                        class="profissional-dropdown-options profissional-horario-dropdown"
                        id="profissional-dropdown-sexta-fim_intervalo">
                        <!-- opções geradas via JS -->
                      </div>
                    </div>
                  </div>

                  <!-- Sábado -->
                  <div class="profissional-column-item">
                    <div class="profissional-dropdown-container">
                      <div class="profissional-input-icon-wrapper">
                        <input
                          type="text"
                          class="profissional-search-input profissional-horario-input"
                          placeholder="00:00h"
                          autocomplete="off"
                          data-day="sabado"
                          data-type="fim_intervalo"
                          name="profissional-fim_intervalo_sabado" />
                        <span class="profissional-input-icon">
                          <span
                            class="material-symbols-outlined"
                            id="profissional-dropdown-icon">
                            keyboard_arrow_down
                          </span>
                        </span>
                      </div>

                      <div
                        class="profissional-dropdown-options profissional-horario-dropdown"
                        id="profissional-dropdown-sabado-fim_intervalo">
                        <!-- opções geradas via JS -->
                      </div>
                    </div>
                  </div>

                  <!-- Domingo -->
                  <div class="profissional-column-item">
                    <div class="profissional-dropdown-container">
                      <div class="profissional-input-icon-wrapper">
                        <input
                          type="text"
                          class="profissional-search-input profissional-horario-input"
                          placeholder="00:00h"
                          autocomplete="off"
                          data-day="domingo"
                          data-type="fim_intervalo"
                          name="profissional-fim_intervalo_domingo" />
                        <span class="profissional-input-icon">
                          <span
                            class="material-symbols-outlined"
                            id="profissional-dropdown-icon">
                            keyboard_arrow_down
                          </span>
                        </span>
                      </div>

                      <div
                        class="profissional-dropdown-options profissional-horario-dropdown"
                        id="profissional-dropdown-domingo-fim_intervalo">
                        <!-- opções geradas via JS -->
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Coluna 5: Horário de Fechamento -->
                <div class="profissional-grid-column">
                  <div class="profissional-column-header">
                    Fim do Expediente
                  </div>

                  <!-- Segunda-feira -->
                  <div class="profissional-column-item">
                    <div class="profissional-dropdown-container">
                      <div class="profissional-input-icon-wrapper">
                        <input
                          type="text"
                          class="profissional-search-input profissional-horario-input"
                          placeholder="00:00h"
                          autocomplete="off"
                          data-day="segunda"
                          data-type="fechamento"
                          name="profissional-fechamento_segunda" />
                        <span class="profissional-input-icon">
                          <span
                            class="material-symbols-outlined"
                            id="profissional-dropdown-icon">
                            keyboard_arrow_down
                          </span>
                        </span>
                      </div>

                      <div
                        class="profissional-dropdown-options profissional-horario-dropdown"
                        id="profissional-dropdown-segunda-fechamento">
                        <!-- opções geradas via JS -->
                      </div>
                    </div>
                  </div>

                  <!-- Terça-feira -->
                  <div class="profissional-column-item">
                    <div class="profissional-dropdown-container">
                      <div class="profissional-input-icon-wrapper">
                        <input
                          type="text"
                          class="profissional-search-input profissional-horario-input"
                          placeholder="00:00h"
                          autocomplete="off"
                          data-day="terca"
                          data-type="fechamento"
                          name="profissional-fechamento_terca" />
                        <span class="profissional-input-icon">
                          <span
                            class="material-symbols-outlined"
                            id="profissional-dropdown-icon">
                            keyboard_arrow_down
                          </span>
                        </span>
                      </div>

                      <div
                        class="profissional-dropdown-options profissional-horario-dropdown"
                        id="profissional-dropdown-terca-fechamento">
                        <!-- opções geradas via JS -->
                      </div>
                    </div>
                  </div>

                  <!-- Quarta-feira -->
                  <div class="profissional-column-item">
                    <div class="profissional-dropdown-container">
                      <div class="profissional-input-icon-wrapper">
                        <input
                          type="text"
                          class="profissional-search-input profissional-horario-input"
                          placeholder="00:00h"
                          autocomplete="off"
                          data-day="quarta"
                          data-type="fechamento"
                          name="profissional-fechamento_quarta" />
                        <span class="profissional-input-icon">
                          <span
                            class="material-symbols-outlined"
                            id="profissional-dropdown-icon">
                            keyboard_arrow_down
                          </span>
                        </span>
                      </div>

                      <div
                        class="profissional-dropdown-options profissional-horario-dropdown"
                        id="profissional-dropdown-quarta-fechamento">
                        <!-- opções geradas via JS -->
                      </div>
                    </div>
                  </div>

                  <!-- Quinta-feira -->
                  <div class="profissional-column-item">
                    <div class="profissional-dropdown-container">
                      <div class="profissional-input-icon-wrapper">
                        <input
                          type="text"
                          class="profissional-search-input profissional-horario-input"
                          placeholder="00:00h"
                          autocomplete="off"
                          data-day="quinta"
                          data-type="fechamento"
                          name="profissional-fechamento_quinta" />
                        <span class="profissional-input-icon">
                          <span
                            class="material-symbols-outlined"
                            id="profissional-dropdown-icon">
                            keyboard_arrow_down
                          </span>
                        </span>
                      </div>

                      <div
                        class="profissional-dropdown-options profissional-horario-dropdown"
                        id="profissional-dropdown-quinta-fechamento">
                        <!-- opções geradas via JS -->
                      </div>
                    </div>
                  </div>

                  <!-- Sexta-feira -->
                  <div class="profissional-column-item">
                    <div class="profissional-dropdown-container">
                      <div class="profissional-input-icon-wrapper">
                        <input
                          type="text"
                          class="profissional-search-input profissional-horario-input"
                          placeholder="00:00h"
                          autocomplete="off"
                          data-day="sexta"
                          data-type="fechamento"
                          name="profissional-fechamento_sexta" />
                        <span class="profissional-input-icon">
                          <span
                            class="material-symbols-outlined"
                            id="profissional-dropdown-icon">
                            keyboard_arrow_down
                          </span>
                        </span>
                      </div>

                      <div
                        class="profissional-dropdown-options profissional-horario-dropdown"
                        id="profissional-dropdown-sexta-fechamento">
                        <!-- opções geradas via JS -->
                      </div>
                    </div>
                  </div>

                  <!-- Sábado -->
                  <div class="profissional-column-item">
                    <div class="profissional-dropdown-container">
                      <div class="profissional-input-icon-wrapper">
                        <input
                          type="text"
                          class="profissional-search-input profissional-horario-input"
                          placeholder="00:00h"
                          autocomplete="off"
                          data-day="sabado"
                          data-type="fechamento"
                          name="profissional-fechamento_sabado" />
                        <span class="profissional-input-icon">
                          <span
                            class="material-symbols-outlined"
                            id="profissional-dropdown-icon">
                            keyboard_arrow_down
                          </span>
                        </span>
                      </div>

                      <div
                        class="profissional-dropdown-options profissional-horario-dropdown"
                        id="profissional-dropdown-sabado-fechamento">
                        <!-- opções geradas via JS -->
                      </div>
                    </div>
                  </div>

                  <!-- Domingo -->
                  <div class="profissional-column-item">
                    <div class="profissional-dropdown-container">
                      <div class="profissional-input-icon-wrapper">
                        <input
                          type="text"
                          class="profissional-search-input profissional-horario-input"
                          placeholder="00:00h"
                          autocomplete="off"
                          data-day="domingo"
                          data-type="fechamento"
                          name="profissional-fechamento_domingo" />
                        <span class="profissional-input-icon">
                          <span
                            class="material-symbols-outlined"
                            id="profissional-dropdown-icon">
                            keyboard_arrow_down
                          </span>
                        </span>
                      </div>

                      <div
                        class="profissional-dropdown-options profissional-horario-dropdown"
                        id="profissional-dropdown-domingo-fechamento">
                        <!-- opções geradas via JS -->
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Rodapé com botões de ação -->
      <div class="profissional-offcanvas-footer">
        <button
          id="profissional-cancel-button"
          class="profissional-cancel-button">
          Cancelar
        </button>
        <button
          id="profissional-save-all-button"
          class="profissional-save-button">
          Salvar
        </button>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
  <script src="../assets/js/profissional.js"></script>
</body>

</html>