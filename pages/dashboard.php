<?php
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
  <link rel="stylesheet" href="../assets/css/sidebar.css">
  <title>Dashboard - Syncronos</title>
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
</body>

</html>