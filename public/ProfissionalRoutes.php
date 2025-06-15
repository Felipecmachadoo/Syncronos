<?php
require_once '../controller/ProfissionalController.php';

$controller = new ProfissionalController();
$rota = $_POST['rota'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  switch ($rota) {
    case 'salvarProfissional':
      $controller->salvarProfissional();
      break;
    case 'excluirProfissional':
      $controller->excluirProfissional();
      break;
  }
}
