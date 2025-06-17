<?php
require_once '../controller/ExpedienteController.php';

if (isset($_GET['idProfissional'])) {
  $controller = new ExpedienteController();
  $controller->getByProfissional($_GET['idProfissional']);
} else {
  http_response_code(400);
  echo json_encode([
    'success' => false,
    'error' => 'ID do profissional não informado'
  ]);
}
