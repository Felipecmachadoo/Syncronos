<?php
require_once '../controller/ServicoController.php';

$controller = new ServicoController();
$rota = $_POST['rota'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  switch ($rota) {
    case 'salvarServico':
      $controller->salvarServico();
      break;
  }
}
