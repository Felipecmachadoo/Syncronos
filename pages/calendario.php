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
  <link href="../assets/css/calendario.css" rel="stylesheet">
  <title>Agenda - FullCalendar</title>
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
        <a href="../app/logout.php">
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
    <div class="container">

      <div id="msg" class="message-container"></div>

      <div id='calendar'></div>
    </div>

    <!-- Modal Visualizar -->
    <div id="visualizarModal" class="modal">
      <div class="modal-content">
        <div class="modal-header">
          <h3 id="visualizarModalLabel">Visualizar Evento</h3>
          <h3 id="editarModalLabel" style="display: none;">Editar Evento</h3>
          <span class="close" data-modal="visualizarModal">&times;</span>
        </div>
        <div class="modal-body">
          <span id="msgViewEvento"></span>

          <div class="visualizarEvento" id="visualizarEvento">

            <div class="info-row">
              <strong>ID:</strong>
              <span id="visualizar_id"></span>
            </div>
            <div class="info-row">
              <strong>Título:</strong>
              <span id="visualizar_title"></span>
            </div>
            <div class="info-row">
              <strong>Início:</strong>
              <span id="visualizar_start"></span>
            </div>
            <div class="info-row">
              <strong>Fim:</strong>
              <span id="visualizar_end"></span>
            </div>

            <button class="btnEditButton" id="btnEditButton">Editar</button>
            <button class="btnDeleteButton" id="btnDeleteButton">Excluir</button>
          </div>

          <div class="editarEvento" id="editarEvento" style="display: none;">
            <form method="POST" id="formEditEvento">

              <input type="hidden" id="edit_id" name="edit_id">

              <input type="hidden" id="dataSelecionada">
              <div class="form-group">
                <label for="tituloEvento">Título:</label>
                <input type="text" id="edit_title" name="edit_title" required>
              </div>
              <div class="form-group">
                <label for="inicioEvento">Início:</label>
                <input type="datetime-local" id="edit_start" name="edit_start" required>
              </div>
              <div class="form-group">
                <label for="fimEvento">Fim:</label>
                <input type="datetime-local" id="edit_end" name="edit_end" required>
              </div>
              <div class="dropdown-container">
                <label>Status</label>
                <div class="dropdown">
                  <div class="dropdown-selected">
                    <span class="color-box confirmado"></span>
                    <span class="dropdown-text">Confirmado</span>
                    <span class="dropdown-arrow">▼</span>
                  </div>
                  <div class="dropdown-options">
                    <div class="option" data-value="confirmado">
                      <span class="color-box confirmado"></span>
                      Confirmado
                    </div>
                    <div class="option" data-value="pendente">
                      <span class="color-box pendente"></span>
                      Pendente
                    </div>
                    <div class="option" data-value="cancelado">
                      <span class="color-box cancelado"></span>
                      Cancelado
                    </div>
                  </div>
                </div>
                <input type="hidden" class="realStatusInput" id="edit_status" name="edit_status" value="confirmado">
              </div>

              <div id="msgEditEvento" class="message-container"></div>

              <button type="button" id="btnViewEvento" class="btnViewEvento">Cancelar</button>

              <button type="submit" id="btnEditEvento" class="btnEditEvento">Salvar</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Cadastrar -->
    <div id="formularioModal" class="custom-modal">
      <div class="custom-modal-content">
        <span class="custom-close">&times;</span>
        <h2 class="modal-title">Agendar Evento</h2>
        <form id="eventoForm">
          <input type="hidden" id="dataSelecionada">
          <div class="form-group">
            <label for="tituloEvento">Título:</label>
            <input type="text" id="tituloEvento" name="cad_title" required>
          </div>
          <div class="form-group">
            <label for="inicioEvento">Início:</label>
            <input type="datetime-local" id="inicioEvento" name="cad_start" required>
          </div>
          <div class="form-group">
            <label for="fimEvento">Fim:</label>
            <input type="datetime-local" id="fimEvento" name="cad_end" required>
          </div>
          <div class="dropdown-container">
            <label>Status</label>
            <div class="dropdown">
              <div class="dropdown-selected">
                <span class="color-box confirmado"></span>
                <span class="dropdown-text">Confirmado</span>
                <span class="dropdown-arrow">▼</span>
              </div>
              <div class="dropdown-options">
                <div class="option" data-value="confirmado">
                  <span class="color-box confirmado"></span>
                  Confirmado
                </div>
                <div class="option" data-value="pendente">
                  <span class="color-box pendente"></span>
                  Pendente
                </div>
                <div class="option" data-value="cancelado">
                  <span class="color-box cancelado"></span>
                  Cancelado
                </div>
              </div>
            </div>
            <input type="hidden" class="realStatusInput" name="cad_status" value="confirmado">
          </div>
          <div id="msgCadEvento" class="message-container"></div>
          <button type="submit" id="btnCadEvento">Salvar</button>
        </form>
      </div>
    </div>
  </div>
  <!-- Overlay para modais -->
  <div id="modalOverlay" class="modal-overlay"></div>

  <script src="../assets/js/calendario/index.global.min.js"></script>
  <script src="../assets/js/calendario/core/locales-all.global.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
  <script src='../assets/js/calendario.js'></script>
</body>

</html>