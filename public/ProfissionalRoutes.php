<?php
require_once '../controller/ProfissionalController.php';
require_once '../controller/ExpedienteController.php';

session_start();

$rota = $_POST['rota'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  switch ($rota) {
    case 'salvarProfissional':
      $controller = new ProfissionalController();
      $controller->salvarProfissional();
      break;
    case 'excluirProfissional':
      $controller = new ProfissionalController();
      $controller->excluirProfissional();
      break;
    case 'salvarExpediente':
      $controller = new ExpedienteController();
      $controller->salvarExpediente();
      break;
    default:
      $_SESSION['erro'] = "Rota não encontrada";
      header("Location: ../pages/profissional.php");
      exit;
  }
}
