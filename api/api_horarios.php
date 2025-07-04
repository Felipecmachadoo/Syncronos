<?php
require_once '../controller/HorarioController.php';

header('Content-Type: application/json');

try {
  $controller = new HorarioController();
  $horarios = $controller->buscarHorarios(); 

  echo json_encode($horarios);
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode([
    'success' => false,
    'error' => 'Erro ao buscar horários: ' . $e->getMessage()
  ]);
}
