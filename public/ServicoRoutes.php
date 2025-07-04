<?php
require_once __DIR__ . '/../controller/ServicoController.php';

$controller = new ServicoController();

$rota = $_POST['rota'] ?? $_GET['rota'] ?? '';

switch ($rota) {
  case 'salvarServico':
    header('Content-Type: application/json');
    echo $controller->salvarServico();
    break;

  case 'buscarServico':
    $idServico = $_GET['idServico'] ?? 0;
    $controller->buscarServicoPorId($idServico);
    break;

  case 'excluirServico':
    $idServico = $_POST['idServico'] ?? 0;
    $result = $controller->excluirServico($idServico);
    header('Content-Type: application/json');
    echo $result; 
    break;

  case 'salvarServico':
    echo $controller->salvarServico();
    break;

  default:
    break;
}
