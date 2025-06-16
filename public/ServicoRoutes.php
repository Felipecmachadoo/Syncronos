<?php
require_once __DIR__ . '/../controller/ServicoController.php';

$controller = new ServicoController();

$rota = $_POST['rota'] ?? $_GET['rota'] ?? '';

switch ($rota) {
  case 'salvarServico':
    $controller->salvarServico();
    break;

  case 'buscarServico':
    $idServico = $_GET['idServico'] ?? 0;
    $controller->buscarServicoPorId($idServico);
    break;

  case 'excluirServico':
    $id = $_POST['id'] ?? 0;
    $success = $controller->excluirServico($id);
    header('Content-Type: application/json');
    echo json_encode(['success' => $success]);
    break;

  default:
    // Rota inválida
    break;
}
